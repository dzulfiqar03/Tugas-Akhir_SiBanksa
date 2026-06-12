<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

import InputLabel from '@/Components/InputLabel.vue';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';

import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5';
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print';
import Responsive from 'datatables.net-responsive-dt';
import DataTable from 'datatables.net-vue3';

import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)
window.JSZip = jszip;

const vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;
pdfMake.vfs = vfs;

const typeForm = ref('Document');
const props = defineProps({
    formdata: Object,
    sidebardata: Object,
    IDUser: Number,
    document: Array,
    image: Array,
    jadwalPelaksanaan: Array,
    IDRT: Number,
    IDRW: Number
});


const showForm = ref(false);
const step = ref(1);
const isEdit = ref(false);
const dtInstance = ref(null);

const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const tableData = computed(() => {
    return typeForm.value === 'Document' ? props.document : props.image;
});

const isPreviewOpen = ref(false);
const selectedImageUrl = ref('');

const openPreview = (fileName) => {
    const safeFileName = encodeURIComponent(fileName.trim());

selectedImageUrl.value = typeForm.value === 'Document' ?
    `/storage/files/documentUser/BankSampah/RT0${props.IDRT}/${safeFileName}` :
    `/storage/photo/evidenceUser/BankSampah/RT0${props.IDRT}/${safeFileName}`;
    isPreviewOpen.value = true;
};

const closePreview = () => {
    isPreviewOpen.value = false;
};

window.handleOpenPreview = openPreview;


const deleteData = (id) => {
    Swal.fire({
        title: 'Hapus data?',
        text: "Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!'
    }).then((res) => {
        if (res.isConfirmed) {
            typeForm.value === 'Document' ?
                router.delete(route('delete-document', id), {
                    onSuccess: () => Swal.fire('Dihapus!', 'Dokumen berhasil dihapus.', 'success')
                }) : router.delete(route('delete-evidence', id), {
                    onSuccess: () => Swal.fire('Dihapus!', 'Evidence berhasil dihapus.', 'success')
                });
        }
    });
};

window.deleteDoc = deleteData;
const formatChildRow = (d) => {

    let photoHtml = typeForm.value === 'Document' ? d.document.map(p => `
        <div class="flex flex-col items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow-sm">
            <div onclick="window.handleOpenPreview('${p.original_filesname}')" class="w-12 h-12 flex flex-col items-center justify-center bg-red-50 text-red-500 rounded-lg border border-red-100">
                <i class="fas fa-file-pdf text-xl"></i>
                <span class="text-[8px] font-bold mt-1">PDF</span>
               </div>
            <span class="text-[10px] text-gray-500 truncate w-20 text-center">${p.original_filesname}</span>
             <div class="flex justify-center gap-1">

                            <button onclick="window.deleteDoc(${p.id})" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
        </div>
    `).join('') : d.photos.map(p => `
        <div class="flex flex-col items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow-sm">
            <img
            src="/storage/photo/evidenceUser/BankSampah/RT0${props.IDRT}/${p.original_photoname}"
            alt="Dokumen" onclick="window.handleOpenPreview('${p.original_photoname}')"
            class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm hover:scale-110 transition-transform cursor-pointer"
        />
            <span class="text-[10px] text-gray-500 truncate w-20 text-center">${p.original_photoname}</span>
                <div class="flex justify-center gap-1">

                            <button onclick="window.deleteDoc(${p.id})" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
        </div>
    `).join('');

    return typeForm.value === 'Document' ? `
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500">
            <p class="text-xs font-bold text-emerald-600 mb-3 uppercase tracking-wider">Dokumen (${d.document.length} File):</p>
            <div class="flex flex-wrap gap-4">
                ${photoHtml || '<p class="text-gray-400 italic">Tidak ada Dokumen.</p>'}
            </div>
        </div>
    `: `
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500">
            <p class="text-xs font-bold text-emerald-600 mb-3 uppercase tracking-wider">Dokumentasi Foto (${d.photos.length} File):</p>
            <div class="flex flex-wrap gap-4">
                ${photoHtml || '<p class="text-gray-400 italic">Tidak ada foto.</p>'}
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
        if (icon) icon.style.transform = 'rotate(0deg)';
    } else {
        row.child(formatChildRow(row.data())).show();
        tr.classList.add('shown');
        if (icon) icon.style.transform = 'rotate(90deg)';
    }
};


const dtOptions = computed(() => ({
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    columns: typeForm.value === 'Document' ? [
        {
            data: null,
            orderable: false,
            className: 'no-print details-control text-center'
        },
        { data: 'name', className: 'text-black dark:text-white', render: (data) => `<strong>Dokumen: ${data}</strong>` },
        { data: 'tanggal_setoran', className: 'text-black dark:text-white', render: (data) => `<strong>Jadwal: ${data}</strong>` },
        {
            data: 'document',
            render: (data) => `<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">${data.length} Dokumen</span>`
        },

    ] : [
        {
            data: null,
            orderable: false,
            className: 'no-print text-black dark:text-white details-control text-center text-black dark:text-white'
        },
        { data: 'name', className: 'text-black dark:text-white', render: (data) => `<strong>Jadwal: ${data}</strong>` },
        {
            data: 'photos',
            render: (data) => `<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">${data.length} Foto</span>`
        },

    ],
    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
    buttons: [
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            pageSize: 'A4',
            title: 'Laporan ' + typeForm.value + ' SiBanksa RT' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            action: async function (e, dt, button, config) {
                const self = this; // Simpan konteks DataTables
                const currentType = typeForm.value;

                Swal.fire({
                    title: 'Memproses PDF...',
                    text: `Menyiapkan lampiran ${currentType.toLowerCase()}...`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // 1. Helper Function
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

                // 2. Pre-fetch Data secara selektif
                const attachmentData = [];
                for (const group of groupedEvidence.value) {
                    const files = [];
                    const isEvidence = currentType === 'Evidence';
                    const source = isEvidence ? (group.photos || []) : (group.document || []);
                    const folder = isEvidence ? 'photo/evidenceUser' : 'files/documentUser';

                    for (const f of source) {
                        const fileName = isEvidence ? f.original_photoname : f.original_filesname;
                        if (!fileName) continue;

                        const url = `${window.location.origin}/storage/${folder}/BankSampah/RT0${props.IDRT}/${fileName}`;
                        let b64 = null;
                        if (isEvidence) b64 = await getBase64(url);

                        files.push({ b64, name: fileName });
                    }
                    if (files.length > 0) {
                        attachmentData.push({ tanggal: group.tanggal_setoran, files });
                    }
                }

                config.customize = function (doc) {
                    Swal.close();
                    const isEv = currentType === 'Evidence';

                    // --- PROTEKSI TABEL UTAMA (DINAMIS) ---
                    const tableNode = doc.content.find(c => c.table);
                    if (tableNode && tableNode.table.body.length > 0) {
                        // Berikan lebar kolom sesuai jumlah kolom pada tab masing-masing
                        if (isEv) {
                            // Tab Evidence punya 3 kolom
                            tableNode.table.widths = [30, '*', 100];
                        } else {
                            // Tab Document punya 4 kolom
                            tableNode.table.widths = [30, '*', 100, 80];
                        }

                        tableNode.table.body.forEach((row, i) => {
                            row.forEach(cell => {
                                if (!cell) return;
                                cell.fontSize = 9;
                                if (i === 0) {
                                    cell.fillColor = '#10b981';
                                    cell.color = 'white';
                                    cell.bold = true;
                                }
                            });
                        });
                    }

                    // --- HEADER (SAMA) ---
                    doc.content.splice(0, 1, {
                        columns: [
                            { stack: [{ text: 'SiBanksa', fontSize: 18, bold: true, color: '#10b981' }, { text: 'Laporan Digital Bank Sampah', fontSize: 7, color: '#9ca3af' }] },
                            { stack: [{ text: `LAPORAN ${currentType.toUpperCase()}`, fontSize: 12, bold: true, alignment: 'right' }, { text: `UNIT RT-0${props.IDRT}`, fontSize: 8, alignment: 'right', color: '#6b7280' }], width: '*' }
                        ],
                        margin: [0, 0, 0, 15]
                    });

                    // --- LAMPIRAN ---
                    if (attachmentData.length > 0) {
                        doc.content.push({
                            text: `\nLAMPIRAN ${isEv ? 'FOTO' : 'BERKAS'}:`,
                            fontSize: 10, bold: true,
                            color: isEv ? '#065f46' : '#b91c1c',
                            margin: [0, 10, 0, 5]
                        });

                        attachmentData.forEach(group => {
                            doc.content.push({
                                table: {
                                    widths: ['*'],
                                    body: [[{ text: `Jadwal: ${group.tanggal}`, bold: true, fontSize: 8, color: isEv ? '#065f46' : '#b91c1c' }]]
                                },
                                layout: { hLineWidth: () => 0, vLineWidth: () => 0, fillColor: isEv ? '#ecfdf5' : '#fef2f2' },
                                margin: [0, 5, 0, 5]
                            });

                            if (isEv) {
                                let columns = [];
                                group.files.forEach((img, i) => {
                                    if (img.b64) {
                                        columns.push({
                                            stack: [
                                                { image: img.b64, width: 100, height: 90, alignment: 'center' },
                                                { text: img.name, fontSize: 5, alignment: 'center', color: '#9ca3af', margin: [0, 2, 0, 0] }
                                            ],
                                            width: '*'
                                        });
                                    }

                                    if (columns.length === 4 || i === group.files.length - 1) {
                                        if (columns.length > 0) {
                                            while (columns.length < 4) columns.push({ text: '', width: '*' });
                                            doc.content.push({ columns: [...columns], columnGap: 10, margin: [0, 5, 0, 10] });
                                            columns = [];
                                        }
                                    }
                                });
                            } else {
                                // RENDER DOKUMEN (LIST VERTIKAL)
                                group.files.forEach(f => {
                                    const fileUrl = `${window.location.origin}/storage/files/documentUser/BankSampah/RT0${props.IDRT}/${f.name}`;
                                    doc.content.push({
                                        text: [
                                            { text: '  • ', color: '#b91c1c', bold: true },
                                            { text: f.name, color: '#2563eb', decoration: 'underline', link: fileUrl, fontSize: 8 },
                                            { text: ' (Klik untuk buka)', fontSize: 7, color: '#9ca3af', italics: true }
                                        ],
                                        margin: [10, 2, 0, 2]
                                    });
                                });
                            }
                        });
                    }
                };

                setTimeout(() => {
                    // Ambil instance tombol secara eksplisit
                    const pdfButton = $.fn.dataTable.ext.buttons.pdfHtml5;
                    if (typeof pdfButton.action === 'function') {
                        pdfButton.action.call(self, e, dt, button, config);
                    } else {
                        console.error("Fungsi PDF asli tidak ditemukan");
                        Swal.close();
                    }
                }, 300);
            }
        },

        // 2. EXPORT EXCEL
        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Dokumen ' + typeForm.value + ' SiBanksa RT' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            exportOptions: { columns: ':not(.no-print)' },
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses Excel...',
                    text: `Menyiapkan lampiran`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (xlsx) {
                    Swal.close();
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c', sheet).attr('s', '25'); // Border tipis semua sel
                    $('row:first c', sheet).attr('s', '51'); // Header Bold & Background
                    $('row:gt(0) c[r^="B"]', sheet).attr('s', '21'); // Bold Kolom Nama/Jadwal
                }
                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                }, 300);
            }
        },

        // 3. PRINT (LENGKAP DENGAN FOTO & NAMA FILE)
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '',
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses Cetak...',
                    text: `Menyiapkan lampiran`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (win) {
                    Swal.close();
                    const tableRows = groupedEvidence.value.map((item, index) => {
                        // Logika Lampiran: Jika Document tampilkan Nama File, Jika Evidence tampilkan Foto + Nama
                        const attachments = typeForm.value === 'Document'
                            ? item.document.map(d => `
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:5px; font-size:10px; color:#dc2626; background:#fef2f2; padding:6px; border-radius:6px; border:1px solid #fee2e2;">
                            <i class="fas fa-file-pdf"></i>
                            <span style="font-weight:600;">${d.original_filesname}</span>
                        </div>
                    `).join('')
                            : item.photos.map(p => `
                        <div style="display:inline-block; text-align:center; margin-right:12px; margin-bottom:12px;">
                            <img src="/storage/photo/evidenceUser/BankSampah/RT0${props.IDRT}/${p.original_photoname}"
                                 style="width:90px; height:90px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; display:block; margin-bottom:5px;">
                            <span style="font-size:9px; color:#6b7280; display:block; max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                ${p.original_photoname}
                            </span>
                        </div>
                    `).join('');

                        return `
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding:15px; text-align:center; vertical-align:top; font-size:12px; color:#9ca3af;">${index + 1}</td>
                        <td style="padding:15px; vertical-align:top;">
                            <div style="font-weight:bold; color:#111827; font-size:14px; margin-bottom:4px; text-transform:uppercase;">${item.name}</div>
                            <div style="font-size:11px; color:#10b981; font-weight:600; margin-bottom:15px;">Jadwal: ${item.tanggal_setoran}</div>
                            <div style="display:flex; flex-wrap:wrap;">
                                ${attachments || '<em style="color:#d1d5db; font-size:11px;">Tidak ada lampiran</em>'}
                            </div>
                        </td>
                        <td style="padding:15px; text-align:center; vertical-align:top;">
                            <span style="background:#f0fdf4; color:#166534; padding:4px 10px; border-radius:12px; font-size:10px; font-weight:800; border:1px solid #bbf7d0;">
                                ${typeForm.value === 'Document' ? item.document.length : item.photos.length} FILE
                            </span>
                        </td>
                    </tr>
                `;
                    }).join('');

                    $(win.document.body).css('font-family', "'Poppins', sans-serif").prepend(`
                <div style="padding: 40px; border-top: 12px solid #10b981; background: #fff;">
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
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">LAPORAN KETUA RW</h2>
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
                    <p style="color: #6b7280; margin: 0;">Lokasi: Unit Bank Sampah RT-0${page.props.auth.user.user_detail?.id_rt || '-'}</p>
                </div>
            </div>
                    <table style="width:100%; border-collapse:collapse; margin-bottom:40px;">
                        <thead>
                            <tr style="background:#f9fafb; text-align:left; font-size:11px; color:#4b5563; text-transform:uppercase; letter-spacing:1px;">
                                <th style="padding:15px; width:40px; text-align:center; border-bottom:2px solid #eee;">No</th>
                                <th style="padding:15px; border-bottom:2px solid #eee;">Rincian Pelaksanaan & Bukti</th>
                                <th style="padding:15px; width:100px; text-align:center; border-bottom:2px solid #eee;">Status</th>
                            </tr>
                        </thead>
                        <tbody>${tableRows}</tbody>
                    </table>
                    <div style="display:flex; justify-content:flex-end; margin-top:50px;">
                        <div style="text-align:center; width:250px;">
                            <p style="font-size:13px; margin-bottom:80px;">Ketua Bank Sampah RT-0${props.IDRT},</p>
                            <p style="font-weight:900; font-size:15px; text-decoration:underline; margin:0; text-transform:uppercase;">${page.props.auth.user.user_detail.fullName}</p>
                            <p style="font-size:11px; color:#9ca3af; margin:0;">NIP: SBK-RT0${props.IDRT}${new Date().getFullYear()}</p>
                        </div>
                    </div>
                </div>
            `);

                    $(win.document.body).find('table').last().hide();
                }

                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);
                }, 300);
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
}));

const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw();
};

const handleCategoryFilter = (e) => {
    const val = e.target.value;

    typeForm.value === 'Document' ?
        dtInstance.value.dt
            .column(1)
            .search(val, true, false)
            .draw() : dtInstance.value.dt
                .column(1)
                .search(val, true, false)
                .draw();
};
const handleLengthChange = (e) => {
    dtInstance.value.dt.page.len(parseInt(e.target.value)).draw();
};

const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const openCreateForm = () => {
    isEdit.value = false;
    form.reset();
    showForm.value = !showForm.value;

};
const form = useForm({

    name: typeForm.value === 'Document' ? '' : [],
    id_userdetail: props.IDUser,
    id_jadwal: '',
    fileDoc: [],
    imgEvidence: []
});

const handleSubmit = () => {

    const url = isEdit.value
        ? (typeForm.value === 'Document' ? route('update-document', form.id) : route('update-evidence', form.id))
        : (typeForm.value === 'Document' ? route('add-document') : route('add-evidence'));
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        forceFormData: true,
        onSuccess: () => {
            isEdit.value
                ? (typeForm.value === 'Document' ? Swal.fire('Berhasil!', 'Dokumen telah diubah.', 'success') : Swal.fire('Berhasil!', 'Evidence telah diubah.', 'success'))
                : (typeForm.value === 'Document' ? Swal.fire('Berhasil!', 'Dokumen telah disimpan.', 'success') : Swal.fire('Berhasil!', 'Evidence telah disimpan.', 'success')
                );


            showForm.value = false;
            form.reset();
        },
        onError: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorHtml = '';
                let totalErrorCount = 0;
                Object.keys(errors).forEach(key => {
                    errors[key].forEach(msg => {
                        errorHtml += ` <li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           ${msg}
                       </li>`;
                        totalErrorCount++;
                    });
                    $(`[name="${key}"]`).addClass('border-red-500 ring-1 ring-red-500');

                });

                $('#error-count').text(totalErrorCount);
                $('#error-list').html(errorHtml);
                $('#error-message').removeClass('hidden').fadeIn();
                Swal.fire('Gagal!', 'Silakan periksa kembali inputan Anda.', 'error');
            } else {
                Swal.fire('Gagal!', 'Silakan periksa kembali inputan Anda.', 'error');
            }
        },

    });
};


const changeTab = (tab) => {
    typeForm.value = tab;
    step.value = 1;
    form.clearErrors();
};

const renamedFileList = computed(() => {
    return typeForm.value === 'Document' ? form.fileDoc.map((file, index) => {
        const extension = file.name.split('.').pop();
        return {
            original: file.name,
            dynamic: `Berkas ${form.name || 'Dokumen'}_BankSampahRT0${props.IDRT}_${index + 1}.${extension}`,
            size: file.size,

        };
    }) : form.imgEvidence.map((file, index) => {
        const extension = file.name.split('.').pop();
        return {
            original: file.name,
            dynamic: `Evidence_${form.name || 'Dokumen'}_BankSampahRT0${props.IDRT}_${index + 1}.${extension}`,
            size: file.size,

        };
    });
});

const groupedEvidence = computed(() => {
    const groups = {};
    // Daftar kata terlarang (Blacklist)
    const blacklist = ['ktp', 'kk', 'akta', 'kartu keluarga'];

    if (typeForm.value === 'Document') {
        props.document.forEach(item => {
            // Cek apakah nama dokumen mengandung kata blacklist
            const isBlacklisted = blacklist.some(word =>
                item.name.toLowerCase().includes(word)
            );

            if (isBlacklisted) return; // Lewati jika termasuk blacklist

            const key = item.id_jadwal;
            if (!groups[key]) {
                const jadwal = props.jadwalPelaksanaan.find(j => j.id === key);
                groups[key] = {
                    id_jadwal: key,
                    name: item.name,
                    tanggal_setoran: jadwal ? jadwal.tanggal_setoran : 'Tanggal Tidak Ditemukan',
                    document: []
                };
            }
            groups[key].document.push(item);
        });
    } else {
        // Logika untuk Evidence/Foto
        props.image.forEach(item => {
            const key = item.name;
            if (!groups[key]) {
                const jadwal = props.jadwalPelaksanaan.find(j => j.tanggal_setoran === item.name);
                groups[key] = {
                    name: item.name,
                    id_userdetail: item.id_userdetail,
                    tanggal_setoran: jadwal ? jadwal.tanggal_setoran : 'Tanggal Tidak Ditemukan',
                    photos: []
                };
            }
            groups[key].photos.push(item);
        });
    }
    return Object.values(groups);
});

const sendReminder = ($id) => {
    showForm.value = false;
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Ketua RW akan menerima notifikasi mengenai pelaporan anda",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('laporsetoran.send-reminder', $id), {

                message: `Pelaporan Baru Hasil setoran pelaksanaan tanggal ${groupedEvidence.value.map(item => item.tanggal_setoran).join(', ')} dari Bank Sampah RT0${props.IDRT}`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Pelaporan Ketua RW', url: route('data-pelaporanRW') },
];
</script>

<template>

    <Head :title="'Data Pelaporan Upload Dokumen'" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Pelaporan Pelaksanaan
                        Bank Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola dokumen dan bukti pelaksanaan bank sampah
                        Anda.</p>
                </div>
                <div class="flex space-x-3">
                    <div v-if="props.document.length > 0 && props.image.length > 0">
                        <button v-if="page.props.auth.user.user_detail.status_transaction !== 'Disetujui'"
                            @click="sendReminder(props.IDRW)"
                            class="flex h-full items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                            <i class="fas fa-bell"></i> Ajukan Persetujuan Ketua RW
                        </button>
                    </div>


                 <div v-else x-cloak
    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/10 dark:border-red-500/20 shadow-sm transition-all">

    <div class="flex-shrink-0">
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    <div class=" text-xs font-medium text-red-800 dark:text-red-300">
        <span v-if="props.document.length == 0">Belum upload hasil setoran</span>
        <span v-else-if="props.image.length == 0">Belum upload bukti evidence</span>
        <span v-else>Mohon lengkapi bukti upload Anda</span>
    </div>
</div>

                    <button @click="openCreateForm"
                        class=" text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95"
                        :class="[
                            showForm ? 'bg-red-500 hover:bg-red-600 shadow-red-500/20' : 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20'
                        ]">

                        <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                        {{ showForm ? 'Tutup Form' : (typeForm === 'Evidence' ? 'Tambah Evidence':'Tambah Dokumen') }}
                    </button>
                </div>

            </div>

            <div class="flex p-1.5 mb-5 bg-gray-100 dark:bg-gray-800/50 rounded-2xl">
                <button @click="changeTab('Document')"
                    :class="typeForm === 'Document' ? 'bg-white shadow-md text-emerald-600' : 'text-gray-500'"
                    class="flex-1 py-3 rounded-xl transition-all font-semibold text-sm">
                    Document
                </button>
                <button @click="changeTab('Evidence')"
                    :class="typeForm === 'Evidence' ? 'bg-white shadow-md text-emerald-600' : 'text-gray-500'"
                    class="flex-1 py-3 rounded-xl transition-all font-semibold text-sm">
                    Evidence
                </button>
            </div>

            <Transition name="accordion">
                <div v-if="showForm"
                    class="bg-white  accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">{{ isEdit ? 'Perbarui Data'
                        : 'Input Data Baru' }}</h3>


                    <div class="flex flex-col w-full ">


                        <FormWrapper v-if="typeForm === 'Document'" formName="formDocument" :errors="form.errors"
                            :processing="form.processing" @submit="handleSubmit">

                            <input type="hidden" name="id_userdetail" v-model="form.id_userdetail">

                            <div class="grid grid-cols-1 gap-4"
                                :class="(form.type === 'file' ? 'md:grid-cols-1' : 'md:grid-cols-2')">

                                <div class="flex flex-col">
                                    <label
                                        class="block mb-1 text-[11px] font-bold text-gray-400 uppercase dark:text-gray-300">Jadwal
                                        Pelaksanaan</label>
                                    <select @change="handleScheduleChange" v-model="form.id_jadwal"
                                        class="text-black w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['id_jadwal'] }">

                                        <option value="" disabled>Pilih Jadwal</option>
                                        <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.id">
                                            {{ j.tanggal_setoran }}
                                        </option>
                                    </select>

                                </div>
                                <template v-for="field in formdata.Dokumen" :key="field.name">

                                    <div v-if="field.name === 'name'" class="flex flex-col">
                                        <InputLabel :for="field.name" :value="field.title" />
                                        <select v-model="form.name"
                                            class="w-full h-11 rounded-xl dark:text-white text-black bg-gray-50 dark:bg-gray-800 border-gray-200 focus:ring-emerald-500 transition-all shadow-sm text-sm pl-5"
                                            :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }">
                                            <option value="">Pilih Jenis Upload</option>
                                            <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}
                                            </option>
                                        </select>

                                    </div>



                                </template>

                                <template v-for="field in formdata.Dokumen" :key="field.name">



                                    <div v-if="field.type === 'file' && field.name === 'fileDoc'" class="flex flex-col">
                                        <InputLabel :for="field.name" :value="field.title" />
                                        <input :type="field.type" :id="field.name" multiple @input="(e) => {
                                            const newFiles = Array.from(e.target.files);
                                            form.fileDoc = [...form.fileDoc, ...newFiles];
                                        }" :placeholder="field.placeholder"
                                            class="w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200"
                                            :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }">
                                        <p v-if="form[field.name]?.length"
                                            class="text-xs text-emerald-600 mt-2 font-medium">
                                            {{ form[field.name].length }} file terpilih
                                        </p>

                                        <ul v-if="form.fileDoc.length > 0" class="mt-2 space-y-1">
                                            <li v-for="(file, index) in renamedFileList" :key="index"
                                                class="text-xs text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1 text-emerald-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                                </svg>
                                                {{ file.dynamic }} ({{ (file.size / 1024).toFixed(1) }} KB)
                                            </li>
                                        </ul>

                                    </div>

                                </template>
                            </div>

                            <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                                <button type="submit"
                                    class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                                    :disabled="form.processing">
                                    <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Dokumen' : 'Simpan Dokumen' }}
                                </button>
                            </div>

                        </FormWrapper>

                        <FormWrapper v-else formName="formEvidence" :errors="form.errors" :processing="form.processing"
                            @submit="handleSubmit">

                            <input type="hidden" name="id_userdetail" v-model="form.id_userdetail">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label
                                        class="block mb-1 text-[11px] font-bold text-gray-400 uppercase dark:text-gray-300">Jadwal
                                        Pelaksanaan</label>
                                    <select @change="handleScheduleChange" v-model="form.name"
                                        class=" text-black  w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['id_jadwal'] }">

                                        <option value="" disabled>Pilih Jadwal</option>
                                        <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.tanggal_setoran">
                                            {{ j.tanggal_setoran }}
                                        </option>
                                    </select>

                                </div>


                                <template v-for="field in formdata.Dokumen" :key="field.name">
                                    <div v-if="field.type === 'file' && field.name === 'imgEvidence'"
                                        class="flex flex-col ">
                                        <InputLabel :for="field.name" :value="field.title" />
                                        <input :type="field.type" :id="field.name" multiple @input="(e) => {
                                            const newFiles = Array.from(e.target.files);
                                            form.imgEvidence = [...form.imgEvidence, ...newFiles];
                                        }" :placeholder="field.placeholder"
                                            class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 text-black e dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200"
                                            :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }">
                                        <p v-if="form[field.name]?.length"
                                            class="text-xs text-emerald-600 mt-2 font-medium">
                                            {{ form[field.name].length }} file terpilih
                                        </p>

                                        <ul v-if="form.imgEvidence.length > 0" class="mt-2 space-y-1">
                                            <li v-for="(file, index) in renamedFileList" :key="index"
                                                class="text-xs text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1 text-emerald-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                                </svg>
                                                {{ file.dynamic }} ({{ (file.size / 1024).toFixed(1) }} KB)
                                            </li>
                                        </ul>

                                    </div>
                                </template>
                            </div>

                            <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                                <button type="submit"
                                    class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                                    :disabled="form.processing">
                                    <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Evidence' : 'Simpan Evidence'
                                    }}
                                </button>
                            </div>
                        </FormWrapper>
                    </div>
                </div>
            </Transition>


            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div v-if="typeForm === 'Document'"
                    class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

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
                            <input @keyup="handleSearch" type="text"
                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                                placeholder="Ketik...">
                        </div>

                        <div class="flex items-center gap-2">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                            <select @change="handleCategoryFilter"
                                class="border border-gray-200 dark:border-gray-600 text-black  rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                                <option value="">Semua</option>
                                <option value="Hasil Setoran">Hasil Setoran</option>
                                <option value="Dokumen Lainnya">Dokumen Lainnya</option>
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

                <div v-else class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

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
                            <input @keyup="handleSearch" type="text"
                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                                placeholder="Ketik...">
                        </div>

                        <div class="flex items-center gap-2">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                            <select @change="handleCategoryFilter"
                                class="border border-gray-200 text-black  dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                                <option value="">Semua</option>
                                <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.tanggal_setoran">
                                    {{ j.tanggal_setoran }}
                                </option>
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

                <div class=" bg-white dark:bg-gray-800 rounded-xl ">
                    <DataTable v-if="typeForm === 'Document'" :data="groupedEvidence" ref="dtInstance"
                        :options="dtOptions" class="w-full display stripe hover cell-border dark:text-white">
                        <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                            <tr>
                                <th></th>
                                <th>Nama Dokumen</th>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Dokumen</th>
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


                    </DataTable>

                    <DataTable v-else :data="groupedEvidence" ref="dtInstance" :options="dtOptions"
                        class="w-full display stripe hover cell-border dark:text-white">
                        <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                            <tr>
                                <th></th>
                                <th>Nama Evidence</th>
                                <th>Evidence</th>
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

                    </DataTable>
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

            <template v-if="typeForm === 'Document'">
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
</style>
