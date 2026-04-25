<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5';
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import Responsive from 'datatables.net-responsive-dt';
import DataTable from 'datatables.net-vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)

const props = defineProps({
    allBankSampah: Array,
    bankSampah: Array,
    bankSampahLog: Array,
    formdata: Object,
    sidebardata: Object
});
const showForm = ref(false);

const isEdit = ref(false);

const form = useForm({
    id: null,
    fullName: '',
    status: '',
    id_gender: 3,
    id_rt: '',
    id_roles: 2,
});
const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const dtInstance = ref(null);

const sendReminder = ($id) => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Bank Sampah akan menerima notifikasi mengenai kekurangan data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('banksampah.send-reminder', $id), {
                message: `Profil dan Dokumen Anda Belum Lengkap, Segera Lengkapi Profil: ${props.notNullProfile}`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};


const viewDetail = (id) => {
    router.get(route('rw.show-banksampah', id));
};

const combinedData = computed(() => {
    const dataAwal = props.bankSampah || [];
    const dataBankSampah = props.allBankSampah || [];

    return dataAwal.map(item => {
        const user = item.user_detail;
        const userId = user?.id;

        const allDocs = (user?.document || []).filter(doc =>
            Number(doc.id_userdetail) === Number(userId)
        );

        const allImgs = (user?.image || []).filter(img =>
            Number(img.id_userdetail) === Number(userId)
        );


        const dataStat = dataBankSampah.find(stat => stat.id === item.id);
        const stats = dataStat?.statistik || {};



        return {
            ...item,
            fullName: user?.fullName || 'Tanpa Nama',
            id_rt: user?.id_rt || '-',

            filtered_documents: allDocs,
            filtered_images: allImgs,
            tanggal_setoran: user?.jadwal?.[0]?.tanggal_setoran || '-',

            total_nasabah: stats?.total_nasabah || 0,
            countOnline: stats?.online_saat_ini || 0,

            statsData: stats,
            nasabah_terverifikasi: stats?.nasabah_terverifikasi,
            nasabah_ditolak: stats?.nasabah_ditolak || 0,
            nasabah_pengajuan: stats?.nasabah_pengajuan,
            nasabah_pending: stats?.nasabah_pending,

        };
    });
});

const isPreviewOpen = ref(false);
const selectedImageUrl = ref('');
const docType = ref('Document');

const openPreview = (fileName, IDRT, type) => {
    selectedImageUrl.value = type === 'Dokumen' ?
        `/storage/files/documentUser/BankSampah/RT0${IDRT}/${fileName}` :
        `/storage/photo/evidenceUser/BankSampah/RT0${IDRT}/${fileName}`;
    docType.value = type;
    isPreviewOpen.value = true;
};

const closePreview = () => {
    isPreviewOpen.value = false;
};

window.handleOpenPreview = openPreview;
const formatChildRow = (d) => {

    const renderRows = (files, type) => {
        if (!files || files.length === 0) {
            return `<tr><td colspan="3" class="text-center py-4 text-gray-400 italic bg-gray-50/50">Tidak ada ${type} untuk jadwal ini</td></tr>`;
        }

        return files.map((f, index) => `
            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <td class="py-3 px-4">
                    <div class="flex items-center dark:text-white text-black gap-3">
                       ${d.tanggal_setoran}
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        ${type === 'Dokumen'
                ? '<i class="fas fa-file-pdf text-red-500 text-lg"></i>'
                : `<img src="/storage/photo/evidenceUser/BankSampah/RT0${d.id_rt}/${f.original_photoname}" class="w-8 h-8 rounded object-cover border">`
            }
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                            ${type === 'Dokumen' ? f.original_filesname : f.original_photoname}
                        </span>
                    </div>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="window.handleOpenPreview('${type === 'Dokumen' ? f.original_filesname : f.original_photoname}', '${d.id_rt}', '${type === 'Dokumen' ? 'Dokumen' : 'Evidence'}')"
                                class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-md text-xs font-bold transition">
                            <i class="fas fa-eye mr-1"></i> LIHAT
                        </button>

                    </div>
                </td>
            </tr>
        `).join('');
    };

    return `
        <div class="p-6 bg-white accordion-wrapper dark:bg-gray-900 border-l-4 border-emerald-500 shadow-inner">

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                <div class="overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            <i class="fas fa-file-invoice-dollar mr-2 text-emerald-500"></i> Lampiran Dokumen
                        </h5>
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                            ${d.filtered_documents.length} FILE
                        </span>
                    </div>
                    <table class="w-full text-left border-collapse border border-gray-100 dark:border-gray-800 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Jadwal</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Nama Berkas</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderRows(d.filtered_documents, 'Dokumen')}
                        </tbody>
                    </table>
                </div>

                <div class="overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            <i class="fas fa-images mr-2 text-blue-500"></i> Foto Bukti Setoran
                        </h5>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">
                            ${d.filtered_images.length} FOTO
                        </span>
                    </div>
                    <table class="w-full text-left border-collapse border border-gray-100 dark:border-gray-800 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Jadwal</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Preview & Nama</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderRows(d.filtered_images, 'Foto')}
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    `;
};

const onRowClick = (event) => {
    const tr = event.target.closest('tr');
    const icon = tr.querySelector('.fa-chevron-right');
    const row = dtInstance.value.dt.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.classList.remove('shown');
        if (icon) icon.style.backgroundColor = 'red';
    } else {
        row.child(formatChildRow(row.data())).show();
        tr.classList.add('shown');
        if (icon) icon.style.backgroundColor = 'black';
    }
};


// --- STATE MOBILE ---
const mobileSearch = ref('');
const mobileCategory = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;

// Filter Data Khusus Mobile
const filteredMobileData = computed(() => {
    let data = combinedData.value;

    if (mobileSearch.value) {
        const s = mobileSearch.value.toLowerCase();
        data = data.filter(item =>
            item.fullName.toLowerCase().includes(s) ||
            item.id_rt.toString().includes(s)
        );
    }

    if (mobileCategory.value) {
        data = data.filter(item => item.id_rt.toString() === mobileCategory.value);
    }

    return data;
});

// Pagination Mobile
const paginatedMobileData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredMobileData.value.slice(start, start + itemsPerPage);
});

const totalMobilePages = computed(() => Math.ceil(filteredMobileData.value.length / itemsPerPage));

// Update Handle Search agar sinkron
const handleSearchSync = (e) => {
    const val = e.target.value;
    handleSearch(e); // Tetap panggil fungsi DataTables asli
    mobileSearch.value = val;
    currentPage.value = 1;
};

const handleCategorySync = (e) => {
    const val = e.target.value;
    handleCategoryFilter(e); // Tetap panggil fungsi DataTables asli
    mobileCategory.value = val;
    currentPage.value = 1;
};

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    columns: [
        {
            data: null,
            orderable: false,
            className: 'no-print text-center'
        },
        {
            data: 'user_detail.fullName',
            className: 'capitalize dark:text-white text-black',
            render: (data, type, row) => {
                return row.user_detail?.fullName || '-';
            },
            defaultContent: '-'
        },

        {
            data: 'user_detail.id_rt',
            className: 'capitalize dark:text-white text-black',
            render: (data, type, row) => {
                return row.user_detail?.id_rt || '-';
            },
            defaultContent: '-'
        },
        {
            data: 'stats',
            className: 'capitalize',
            render: (data, type, row) => {
                return `
                <div class="grid gap-2">
                      <span class="w-full dark:text-white text-black px-4 rounded-xl text-sm">Total: ${row.total_nasabah}</span>

                <div class="grid grid-cols-2 gap-2">
                    <span class="bg-yellow-500 w-full px-4 rounded-xl text-sm">Pengajuan: ${row.nasabah_pengajuan}</span>
                     <span class="bg-emerald-500 w-full px-4 rounded-xl text-sm">Disetujui: ${row.nasabah_terverifikasi}</span>
                    <span class="bg-red-500 w-full px-4 rounded-xl text-sm">Ditolak: ${row.nasabah_ditolak}</span>
                    <span class="bg-gray-500 w-full px-4 rounded-xl text-sm">Pending: ${row.nasabah_pending}</span>

                    </div>
                    </div>

                `;
            },
            defaultContent: '-'
        },
        {
            data: 'total_setoran_rt',
            defaultContent: 0, // Solusi utama menghilangkan warning
            render: (data) => {
                const total = parseFloat(data || 0);
                return `<strong class="text-emerald-600">Rp ${new Intl.NumberFormat('id-ID').format(total)}</strong>`;
            }
        },
        {
            data: null,
            orderable: false,
            className: 'no-print text-center'
        },


    ],
    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
    buttons: [
        // --- 1. TOMBOL PDF ---
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            pageSize: 'A4',
            exportOptions: {
                columns: ':not(.no-print)'
            },
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Laporan_Pelaporan_SiBanksa_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            action: async function (e, dt, button, config) {
                const self = this;
                Swal.fire({ title: 'Memproses PDF...', text: 'Menyiapkan lampiran foto...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                const getBase64 = (url) => {
                    return new Promise((resolve) => {
                        const img = new Image();
                        img.setAttribute("crossOrigin", "anonymous");
                        img.onload = () => {
                            const canvas = document.createElement("canvas");
                            canvas.width = img.width; canvas.height = img.height;
                            const ctx = canvas.getContext("2d");
                            ctx.drawImage(img, 0, 0);
                            resolve(canvas.toDataURL("image/png"));
                        };
                        img.onerror = () => resolve(null);
                        img.src = url;
                    });
                };

                const attachmentData = [];
                for (const item of combinedData.value) {
                    const photos = [];
                    for (const img of item.filtered_images) {
                        const url = `${window.location.origin}/storage/photo/evidenceUser/BankSampah/RT0${item.id_rt}/${img.original_photoname}`;
                        const b64 = await getBase64(url);
                        if (b64) photos.push({ b64, name: img.original_photoname });
                    }
                    if (photos.length > 0 || item.filtered_documents.length > 0) {
                        attachmentData.push({ namaBank: item.fullName, rt: item.id_rt, tanggal: item.tanggal_setoran, photos, docs: item.filtered_documents });
                    }
                }

                config.customize = function (doc) {
                    Swal.close();
                    // Styling Tabel Utama
                    const tableNode = doc.content.find(c => c.table);
                    if (tableNode) {
                        tableNode.table.widths = [30, '*', 40, 140, 100];
                        tableNode.table.body.forEach((row, i) => {
                            row.forEach(cell => { if (cell) cell.fontSize = 8; if (i === 0) { cell.fillColor = '#10b981'; cell.color = 'white'; } });
                        });
                    }

                    // Header & Lampiran
                    doc.content.splice(0, 1, {
                        columns: [
                            { stack: [{ text: 'SiBanksa', fontSize: 18, bold: true, color: '#10b981' }, { text: 'Laporan Monitoring Bank Sampah', fontSize: 7, color: '#9ca3af' }] },
                            { stack: [{ text: 'DATA PELAPORAN UNIT', fontSize: 12, bold: true, alignment: 'right' }, { text: `Unit RW - Gresik`, fontSize: 8, alignment: 'right' }], width: '*' }
                        ], margin: [0, 0, 0, 15]
                    });

                    if (attachmentData.length > 0) {
                        doc.content.push({ text: '\nLAMPIRAN DOKUMENTASI:', fontSize: 10, bold: true, color: '#065f46', margin: [0, 20, 0, 10] });
                        attachmentData.forEach(group => {
                            doc.content.push({
                                table: { widths: ['*'], body: [[{ text: `${group.namaBank} (RT-0${group.rt})`, bold: true, fontSize: 8, color: '#065f46' }]] },
                                layout: { hLineWidth: () => 0, vLineWidth: () => 0, fillColor: '#ecfdf5' }, margin: [0, 5, 0, 5]
                            });

                            // Render List Berkas
                            group.docs.forEach(d => {
                                doc.content.push({ text: `• Berkas: ${d.original_filesname}`, fontSize: 7, margin: [10, 0, 0, 2] });
                            });

                            // Render Grid Foto
                            let columns = [];
                            group.photos.forEach((img, i) => {
                                columns.push({ stack: [{ image: img.b64, width: 90, height: 80, alignment: 'center' }, { text: img.name, fontSize: 5, alignment: 'center', color: '#9ca3af' }], width: '*' });
                                if (columns.length === 4 || i === group.photos.length - 1) {
                                    while (columns.length < 4) columns.push({ text: '', width: '*' });
                                    doc.content.push({ columns: [...columns], columnGap: 10, margin: [0, 5, 0, 10] });
                                    columns = [];
                                }
                            });
                        });
                    }
                };
                setTimeout(() => { $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config); }, 500);
            }
        },

        // --- 2. TOMBOL EXCEL ---
        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Data_Pelaporan_SiBanksa',
            exportOptions: { columns: ':not(.no-print)' },
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];
                $('row c', sheet).attr('s', '25'); // Tambah border ke semua cell
                $('row:first c', sheet).attr('s', '42'); // Header Hijau (Style default DT)
            }
        },

        // --- 3. TOMBOL PRINT ---
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            exportOptions: { columns: ':not(.no-print)' },
            customize: function (win) {
                $(win.document.body).css('font-family', 'Poppins, sans-serif').prepend(`
                       <div style="padding: 40px; border-top: 10px solid #10b981; background: white;">
            <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-recycle text-[20rem]"></i>
                </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div
                            class="w-16 h-16 bg-emerald-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                            <i class="fas fa-leaf"></i>
                        </div>
                    <div>
                        <h1 style="margin: 0; font-size: 24px; font-weight: 900; color: #1f2937;">SiBanksa</h1>
                        <p style="margin: 0; font-size: 10px; color: #6b7280; font-weight: bold; letter-spacing: 1px;">SISTEM INFORMASI BANK SAMPAH</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">LAPORAN BANK SAMPAH</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 40px; font-size: 14px;">
                <div>
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DITERIMA DARI:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${page.props.auth.user.user_detail.fullName}</p>
                    <p style="color: #6b7280; margin: 0;">${page.props.auth.user.user_detail.roles.role} SiBanksa</p>
                    <p style="color: #6b7280; margin: 0;">RT: ${page.props.auth.user.user_detail?.id_rt || '-'} / RW: 01</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">Dicetak Pada:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${new Date().toLocaleDateString('id-ID')}</p>
                </div>
            </div>
            `);

                // Styling Tabel di Print
                $(win.document.body).find('table').addClass('compact').css({ 'font-size': '10px', 'color': '#333' });
                $(win.document.body).find('thead').css({ 'background-color': '#10b981', 'color': '#fff' });

                // Menambahkan Lampiran Foto di akhir halaman print
                let photoHtml = '<div style="margin-top:30px;"><h3>LAMPIRAN DOKUMENTASI</h3>';
                combinedData.value.forEach(item => {
                    if (item.filtered_images.length > 0) {
                        photoHtml += `<div style="background:#f9fafb; padding:10px; border-radius:8px; margin-bottom:15px; border:1px solid #e5e7eb;">
                        <h4 style="margin:0 0 10px 0; color:#065f46;">RT-0${item.id_rt} - ${item.fullName}</h4>
                        <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:10px;">`;
                        item.filtered_images.forEach(img => {
                            photoHtml += `<div style="text-align:center;">
                            <img src="/storage/photo/evidenceUser/BankSampah/RT0${item.id_rt}/${img.original_photoname}" style="width:100%; height:100px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                            <p style="font-size:8px; color:#777; margin-top:4px;">${img.original_photoname}</p>
                        </div>`;
                        });
                        photoHtml += `</div></div>`;
                    }
                });
                photoHtml += '</div>';
                $(win.document.body).append(photoHtml);
            }
        }
    ],
    language: {
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
            previous: "← Sebelumnya",
            next: "Berikutnya →"
        },
        emptyTable: "Tidak ada data tersedia"
    }
};

const prevPage = () => dtInstance.value.dt.page('previous').draw('page');
const nextPage = () => dtInstance.value.dt.page('next').draw('page');
const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw();
};

const handleCategoryFilter = (e) => {
    const val = e.target.value;
    // ^ artinya awal kata, $ artinya akhir kata (pencarian eksak)
    const regex = val ? `^${val}$` : '';

    dtInstance.value.dt
        .column(2)
        .search(regex, true, false) // parameter kedua 'true' mengaktifkan regex
        .draw();
};
const handleLengthChange = (e) => {
    dtInstance.value.dt.page.len(parseInt(e.target.value)).draw();
};

const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};


const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Pelaporan Bank Sampah', url: route('data-pelaporanBankSampah') },
];

const updateVerification = (item) => {
    Swal.fire({
        title: 'Lakukan Pembukaan Transaksi?',
        text: "Bank sampah RT0" + item.user_detail.id_rt + " akan dapat melakukan transaksi dan notifikasi mengenai pelaporan anda",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('rw.open-transaction', item.user_detail.id), {

                message: `Pembukaan Transaksi berhasil dibuka dan notifikasi berhasil dikirim ke Bank Sampah RT0${item.user_detail.id_rt}`
            }, {
                onSuccess: () => { Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success'), window.location.reload() }
            });
        }
    });
};

</script>

<template>

    <Head :title="'Data Pelaporan Bank Sampah'" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Kelola Bank Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola bank sampah di RW anda.</p>
                </div>

            </div>


            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

                    <div class="flex flex-wrap mb-5 lg:mb-0 items-center gap-2">
                        <button @click="exportData(0)"
                            class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button @click="exportData(1)"
                            class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button @click="exportData(2)"
                            class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                    <div class="flex flex-wrap md:flex-nowrap items-end justify-start gap-3">
                        <div class="flex items-end gap-2">
                            <label
                                class="text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                            <input @keyup="handleSearchSync" type="text"
                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                                placeholder="Ketik...">
                        </div>

                        <div class="flex items-center gap-2">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                            <select @change="handleCategorySync"
                                class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                                <template v-for="field in formdata.nasabah" :key="field.name">
                                    <div v-if="field.name === 'rt'" class="col-span-1">
                                        <option value="">Pilih RT</option>
                                        <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                    </div>
                                </template>

                            </select>
                        </div>

                        <div class="flex items-center gap-2  pl-3">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                            <select @change="handleLengthChange"
                                class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class=" hidden md:block bg-white dark:bg-gray-800 rounded-xl shadow">
                    <DataTable :data="combinedData" ref="dtInstance" :options="dtOptions"
                        class="w-full display stripe hover cell-border dark:text-white">
                        <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                            <tr>

                                <th class="px-6 py-4"></th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">RT</th>
                                <th class="px-6 py-4">Data Nasabah</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>


                        <template #column-0="data">
                            <div class="flex justify-center gap-2">


                                <button @click="onRowClick"
                                    class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
                                    title="Edit">
                                    <i class="fas fa-plus-circle text-emerald-500 cursor-pointer"></i>
                                </button>

                            </div>
                        </template>



                        <template #column-5="data">
                            <div v-if="data.rowData.user_detail.status_transaction === 'Belum Disetujui'"
                                class="flex justify-center gap-1">
                                <button @click="updateVerification(data.rowData)"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                    <i class="fas fa-bell"></i> Buka Transaksi
                                </button>
                            </div>

                            <div v-else class="flex justify-center gap-1">
                                <button disabled
                                    class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500  text-white text-[11px] font-bold rounded-lg transition">
                                    <i class="fas fa-bell"></i> Transaksi sudah dibuka
                                </button>
                            </div>
                        </template>



                    </DataTable>
                </div>

                <div class="block md:hidden space-y-4">
    <div v-for="item in paginatedMobileData" :key="item.id"
        class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 relative">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white capitalize">{{ item.fullName }}</h4>
                <p class="text-[10px] font-bold text-emerald-600">RT-0{{ item.id_rt }} • RW-01</p>
            </div>
            <span :class="[
                'px-2 py-1 rounded-lg text-[9px] font-bold uppercase',
                item.user_detail.status_transaction === 'Belum Disetujui' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'
            ]">
                {{ item.user_detail.status_transaction }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                <p class="text-[9px] text-gray-400 font-bold uppercase">Total Nasabah</p>
                <p class="text-sm font-bold dark:text-white">{{ item.total_nasabah }}</p>
            </div>
            <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                <p class="text-[9px] text-gray-400 font-bold uppercase">Total Setoran</p>
                <p class="text-sm font-bold text-emerald-600">Rp {{ new Intl.NumberFormat('id-ID').format(item.total_setoran_rt || 0) }}</p>
            </div>
        </div>

        <div class="flex gap-1 mb-4">
            <div class="flex-1 h-1.5 bg-yellow-400 rounded-full" :title="'Pengajuan: '+item.nasabah_pengajuan"></div>
            <div class="flex-1 h-1.5 bg-emerald-400 rounded-full" :title="'Setuju: '+item.nasabah_terverifikasi"></div>
            <div class="flex-1 h-1.5 bg-red-400 rounded-full" :title="'Tolak: '+item.nasabah_ditolak"></div>
            <div class="flex-1 h-1.5 bg-gray-400 rounded-full" :title="'Pending: '+item.nasabah_pending"></div>
        </div>

        <div class="flex gap-2">
            <button @click="viewDetail(item.id)"
                class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200 py-2 rounded-xl text-xs font-bold flex items-center justify-center gap-2">
                <i class="fas fa-eye"></i> DETAIL
            </button>

            <button v-if="item.user_detail.status_transaction === 'Belum Disetujui'"
                @click="updateVerification(item)"
                class="flex-[1.5] bg-red-500 text-white py-2 rounded-xl text-xs font-bold shadow-lg shadow-red-500/20">
                <i class="fas fa-bell mr-1"></i> BUKA TRANSAKSI
            </button>
            <button v-else disabled
                class="flex-[1.5] bg-emerald-500/10 text-emerald-600 py-2 rounded-xl text-xs font-bold border border-emerald-500/20">
                <i class="fas fa-check-circle mr-1"></i> AKTIF
            </button>
        </div>
    </div>

    <div v-if="totalMobilePages > 1" class="flex items-center justify-between px-2 pt-4 pb-10">
        <button @click="currentPage--" :disabled="currentPage === 1"
            class="p-2 text-gray-500 disabled:opacity-20"><i class="fas fa-chevron-left"></i></button>
        <span class="text-xs font-bold text-gray-400">Halaman {{ currentPage }} dari {{ totalMobilePages }}</span>
        <button @click="currentPage++" :disabled="currentPage === totalMobilePages"
            class="p-2 text-gray-500 disabled:opacity-20"><i class="fas fa-chevron-right"></i></button>
    </div>
    </div>

            </div>


        </div>
    </AuthenticatedLayout>

    <div v-if="isPreviewOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        @click.self="closePreview">

        <div class="relative max-w-4xl w-full flex flex-col items-center">
            <button @click="closePreview"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <template v-if="docType === 'Dokumen'">
                <div class="w-full h-[80vh] md:h-[85vh]">
                    <embed :src="selectedImageUrl" type="application/pdf"
                        class="w-full h-full rounded-lg shadow-inner" />
                </div>
            </template>

            <template v-else>
                <div class="w-full h-full flex items-center justify-center p-4">
                    <img :src="selectedImageUrl" class="max-w-full max-h-full object-contain" alt="Preview Image">
                </div>
            </template>

            <p class="mt-4 text-white text-sm font-medium">Klik di mana saja untuk menutup</p>
        </div>
    </div>
</template>

<style>
.dark td {
    color: white;
}

.progress-flow {
    width: 100%;
    background: linear-gradient(110deg,
            #3b82f6 25%,
            #60a5fa 37%,
            #3b82f6 63%);
    background-size: 200% 100%;
    animation: flow 1.2s linear infinite;
}

@keyframes flow {
    from {
        background-position: 200% 0;
    }

    to {
        background-position: -200% 0;
    }
}

.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease-in-out;
    max-height: 500px;
    overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
    margin-top: 0;
    margin-bottom: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.accordion-wrapper>* {
    transition: opacity 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #10b981 !important;
    border: none !important;
    color: white !important;
    border-radius: 8px;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: 0.8rem;
    color: #ffffff !important;
    margin-top: 1rem;
}

.dark .dataTables_wrapper .dataTables_length,
.dark .dataTables_wrapper .dataTables_filter,
.dark .datatable .dt-info,
.dark .dataTables_wrapper .dataTables_processing,
.dark .datatable .dt-paging {
    color: #ffffff !important;
}

.dataTables_filter {
    display: none;
}

/* Kita pakai custom search di atas */

.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>
