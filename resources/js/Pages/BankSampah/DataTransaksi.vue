<script setup>
import { ref, computed, render } from 'vue';
import { useForm, router, Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';

// ================= DATATABLES =================
import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net'
import Buttons from 'datatables.net-buttons'
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5'
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print'
import Responsive from 'datatables.net-responsive-dt'

// CSS (WAJIB)
import 'datatables.net-dt/css/dataTables.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)
window.JSZip = jszip;
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;
const props = defineProps({
    formdata: Object,
    items: Array,
    sidebardata: Object,
    document: Array,

    breadcrumbItems: Array,
    user: Object,
    transaction: Array,
    nasabah: Array,
    nasabahAll: Array,
    reporting: Array,
    countTransaction: Number,
    IDRW: Number,
    IDRT: Number

});


console.log(props.nasabah)
// State
const showForm = ref(false);
const showDetail = ref(false);
const selectedNasabah = ref(null);
const isEdit = ref(false);

const form = useForm({
    id: props.user.id,
    id_userdetail: props.user.user_detail.id,
    id_userbank: '',
    id_jadwal: '',
    fullName: '',
    pencatatan_setoran_id: '',
    bukti_pembayaran: '',
    fileDoc: []


});

const viewDetail = (id) => {
    // Navigasi ke halaman detail nasabah
    router.get(route('show-nasabah', id));
};

const renamedFileList = computed(() => {
    form.fileDoc.map((file, index) => {
        const extension = file.name.split('.').pop();
        return {
            original: file.name,
            dynamic: `Dokumen${form.name || 'Dokumen'}_BankSampahRT0${props.IDRT}_${index + 1}.${extension}`,
            size: file.size,

        };
    })

});


const editData = (item) => {
    const row = JSON.parse(decodeURIComponent(escape(atob(item))));

    console.log(row)
    isEdit.value = true;
    form.id = row.user_detail.id_user;
    form.fullName = row.user_detail.fullName;
    form.id_userdetail = row.id_userdetail;
    form.id_jadwal = row.id_jadwal;
    form.id_userbank = row.user_bank[0].id;
    form.pencatatan_setoran_id =
        row.pencatatan_items.find(i => i.pencatatan_setoran_id)?.pencatatan_setoran_id ?? null;
    form.bukti_pembayaran = '';
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.uploadBukti = editData

const deleteData = (base64) => {
    const row = JSON.parse(decodeURIComponent(escape(atob(base64))));
    Swal.fire({
        title: 'Hapus data?',
        text: "Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!'
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('bs.delete-transaction', row.id), {
                onSuccess: () => Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
            });
        }
    });
};

window.handleDelete = deleteData

const kirimWA = (base64) => {
    const row = JSON.parse(decodeURIComponent(escape(atob(base64))));
    Swal.fire({
        title: 'Lakukan Pembukaan Transaksi?',
        text: "Bank sampah RT0" + props.IDRT + " akan dapat melakukan transaksi dan notifikasi mengenai pelaporan anda",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('bs.chat-transaction', row.user_detail.id_user), {

                message: `Anda Belum mengisi rekening dan tidak bisa dicairkan, Isi dan lengkapi rekening terlebih dahulu!!`
            }, {
                onSuccess: () => { Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success'), window.location.reload() }
            });
        }
    });
}

const kirimWA2 = (row) => {
    const nomorWA = "6281216299698"; // Ganti dengan nomor admin/bank sampah
    const nama = row;

    // Template pesan
    const pesan = `Halo Admin, saya ${nama}. Saya ingin mengonfirmasi setoran sampah saya sebesar Rp. Mohon segera diproses ya!`;

    // Encode pesan agar aman di URL
    const link = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`;

    // Buka tab baru
    window.open(link, '_blank');
};
window.handleWA = kirimWA;

const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const dtInstance = ref(null);
const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],
    columns: [
        {
            data: null,
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            // Langsung akses user_detail (tanpa kata 'jadwal')
            data: 'user_detail.fullName',
            className: 'text-black dark:text-white capitalize',
            render: (data, type, row) => {
                return row.user_detail.fullName || '-';
            },
            defaultContent: '-'
        },
        {
            data: 'user_bank',
            className: 'text-black dark:text-white capitalize',

            render: (data, type, row) => {
                return row.user_bank[0].nomor_rekening;
            },

        },

        {
            data: 'user_bank',
            className: 'text-black dark:text-white capitalize',

            render: (data, type, row) => {
                return row.user_bank[0].bank.short_name || '-';
            }
        },

        {
            data: 'pencatatan_items',
            className: 'text-black dark:text-white capitalize',

            render: (data, type, row) => {

                const total = data.reduce((acc, item) => acc + parseFloat(item.subtotal), 0);

                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(total);

                return `<div class="font-bold text-blue-600">${formatted}</div>`;
            },
            defaultContent: 'Rp 0'
        },
        {
            // Kolom 3: Status (Penting untuk filter kategori)
            data: 'user_bank',
            className: 'text-black dark:text-white capitalize',

            render: (data, type, row) => {
                // Menyesuaikan dengan badge di template
                const status = row.user_bank.length === 0 ? 'Belum' : 'Selesai';
                return `<span class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-700">${status}</span>`;
            },
            className: 'text-center'
        },


        {
            // Kolom 4: Aksi
            data: null,
            orderable: false,
            className: 'no-print text-center',
            render: (data, type, row) => {
                const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(row))));
                return row.user_transaction.length === 0 ? !row.user_bank || row.user_bank.length === 0 ? ` <button
                                            onclick="window.handleWA('${base64Data}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Hubungi WA
                                        </button>`: ` <button
                                            onclick="window.uploadBukti('${base64Data}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Kirim Bukti Pembayaran
                                        </button>`: `

                                        <div class="flex space-x-3">
                                            <button
                                            onclick=""
                                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-check"></i> Transaksi Telah Dilakukan
                                        </button>

                                        <button
                                            onclick="window.handleDelete('${base64Data}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-trash"></i> Hapus Transaksi
                                        </button>
                                            </div>`
            }

        }
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
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            pageSize: 'A4',
            title: 'Laporan Transaksi SiBanksa ' +
                'RT-0' + (page.props.auth.user.user_detail?.id_rt || '-') + ' ' +
                new Date().toLocaleDateString('id-ID').replace(/\//g, '-'), customize: function (doc) {
                    // 1. Hitung Total
                    const totalSemua = props.nasabah?.reduce((acc, n) => {
                        const saldo = n.pencatatan_items?.reduce((s, i) => s + (parseFloat(i.subtotal) || 0), 0) || 0;
                        return acc + saldo;
                    }, 0) || 0;

                    const formattedTotal = new Intl.NumberFormat('id-ID', {
                        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                    }).format(totalSemua);

                    // 2. Cari Tabel Utama
                    const tableNode = doc.content.find(c => c.table);

                    if (tableNode) {
                        const rowCount = tableNode.table.body.length;
                        const colCount = tableNode.table.body[0].length; // Hitung jumlah kolom otomatis

                        // FIXER: Pastikan semua baris yang sudah ada TIDAK memiliki sel undefined
                        tableNode.table.body.forEach(row => {
                            for (let i = 0; i < colCount; i++) {
                                if (typeof row[i] === 'undefined' || row[i] === null) {
                                    row[i] = { text: '' };
                                }
                            }
                        });

                        // Atur Lebar (Pastikan array ini jumlahnya sama dengan colCount)
                        // Jika tabelmu punya 6 kolom, gunakan ini:
                        if (colCount === 6) {
                            tableNode.table.widths = [25, '*', 100, 60, 80, 50];
                        }

                        // 3. TAMBAHKAN BARIS TOTAL DENGAN LOGIKA DINAMIS
                        // Kita buat array kosong, lalu isi sesuai jumlah kolom agar tidak Malformed
                        let rowTotal = [];

                        // Kolom pertama dengan ColSpan (mengambil jatah kolom 1 sampai kolom n-2)
                        rowTotal.push({
                            text: 'TOTAL SETORAN BELUM CAIR',
                            colSpan: colCount - 2,
                            alignment: 'right',
                            bold: true,
                            fillColor: '#f9fafb'
                        });

                        // Isi placeholder kosong untuk kolom yang kena ColSpan
                        for (let i = 0; i < colCount - 3; i++) {
                            rowTotal.push({});
                        }

                        // Kolom Saldo (Kolom ke-5)
                        rowTotal.push({
                            text: formattedTotal,
                            bold: true,
                            color: '#10b981',
                            alignment: 'right',
                            fillColor: '#f0fdf4'
                        });

                        // Kolom Status (Kolom ke-6 / Terakhir)
                        rowTotal.push({ text: '', fillColor: '#f9fafb' });

                        // Masukkan ke tabel
                        tableNode.table.body.push(rowTotal);
                        tableNode.layout = 'lightHorizontalLines';
                    }

                    // 4. Header & Tanda Tangan (Sama seperti sebelumnya)
                    const userDetail = page.props.auth.user?.user_detail;
                    const idRT = userDetail?.id_rt || '-';

                    doc.content.splice(0, 1,
                        {
                            columns: [
                                {
                                    stack: [
                                        { text: 'SiBanksa', fontSize: 22, bold: true, color: '#10b981' },
                                        { text: 'Sistem Informasi Bank Sampah Digital', fontSize: 8, color: '#6b7280' },
                                    ]
                                },
                                {
                                    stack: [
                                        { text: 'LAPORAN TRANSAKSI', fontSize: 16, bold: true, alignment: 'right' },
                                        { text: `UNIT RT-0${idRT}`, fontSize: 10, alignment: 'right', color: '#9ca3af' },
                                    ],
                                    width: '*'
                                }
                            ]
                        },
                        { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 515, y2: 5, lineWidth: 1, lineColor: '#10b981' }], margin: [0, 5, 0, 15] }
                    );

                    doc.content.push(
                        { text: '\n\n' },
                        {
                            columns: [
                                { text: '', width: '*' },
                                {
                                    width: 180,
                                    stack: [
                                        { text: `Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`, alignment: 'center' },
                                        { text: 'Verifikator Lapangan', alignment: 'center', margin: [0, 5, 0, 40] },
                                        { text: `( ${userDetail?.fullName || '..........................'} )`, alignment: 'center', bold: true },
                                        { text: 'ID: SBK-RT0' + idRT, alignment: 'center', fontSize: 8, color: '#9ca3af' }
                                    ]
                                }
                            ]
                        }
                    );

                    doc.styles.tableHeader = { fillColor: '#10b981', color: 'white', bold: true, alignment: 'center' };
                }
        },

        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Dokumen Transaksi SiBanksa ' +
                'RT-0' + (page.props.auth.user.user_detail?.id_rt || '-') + ' ' +
                new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            exportOptions: {
                columns: ':not(.no-print)'
            },
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];

                // 1. Hitung Total
                const totalSemua = props.nasabah?.reduce((acc, n) => {
                    const saldo = n.pencatatan_items?.reduce((s, i) => s + (parseFloat(i.subtotal) || 0), 0) || 0;
                    return acc + saldo;
                }, 0);

                // 2. Berikan Border ke Semua Cell yang memiliki data
                // Kita cari semua tag <c> (cell) dan beri style '25' (border tipis)
                // Kecuali header yang akan kita beri style berbeda nanti
                $('row c', sheet).attr('s', '25');

                // 3. Styling Header (Style 51: Bold, Background Grey, Border)
                $('row:first c', sheet).attr('s', '51');

                // 4. Atur Lebar Kolom
                var colConf = [
                    { id: 'A', width: 5 }, { id: 'B', width: 30 },
                    { id: 'C', width: 20 }, { id: 'D', width: 15 },
                    { id: 'E', width: 20 }, { id: 'F', width: 15 }
                ];
                colConf.forEach((c, i) => {
                    $(`col[min="${i + 1}"]`, sheet).attr('width', c.width);
                });

                // 5. Tambahkan Baris Total dengan Border & Warna
                var lastRow = $('row', sheet).length;
                var nextRow = lastRow + 1;

                // Style 67: Hijau/Success dengan border, Style 51: Grey/Bold dengan border
                var totalRow = `
        <row r="${nextRow}" customHeight="1" ht="30">
            <c r="A${nextRow}" t="inlineStr" s="51">
                <is><t>TOTAL KESELURUHAN SETORAN</t></is>
            </c>
            <c r="B${nextRow}" s="51"></c>
            <c r="C${nextRow}" s="51"></c>
            <c r="D${nextRow}" s="51"></c>
            <c r="E${nextRow}" t="n" s="67">
                <v>${totalSemua}</v>
            </c>
            <c r="F${nextRow}" s="51"></c>
        </row>
    `;

                $('sheetData', sheet).append(totalRow);

                // 6. Merge Cell untuk Label Total
                if (!$('mergeCells', sheet).length) {
                    $('worksheet', sheet).prepend('<mergeCells count="1"/>');
                }
                $('mergeCells', sheet).append(`<mergeCell ref="A${nextRow}:D${nextRow}"/>`);
            }
        },
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '',
            customize: function (win) {

                const totalSemuaNasabah = props.nasabah?.reduce((acc, nasabah) => {

                    const saldoNasabah = nasabah.pencatatan_items?.reduce((subAcc, item) => {
                        return subAcc + (parseFloat(item.subtotal) || 0);
                    }, 0) || 0;

                    return acc + saldoNasabah;
                }, 0);

                const formattedTotalSemua = new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(totalSemuaNasabah);


                const tableRows = props.nasabah?.map((item, index) => {
                    // Hitung saldo per individu untuk ditampilkan di kolom "Total Saldo"
                    const saldoIndividu = item.pencatatan_items?.reduce((acc, p) => acc + (parseFloat(p.subtotal) || 0), 0) || 0;

                    const formattedIndividu = new Intl.NumberFormat('id-ID', {
                        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                    }).format(saldoIndividu);

                    return `
            <tr style="border-bottom: 1px solid #f3f4f6;">
                <td style="padding: 12px; text-align: center;">${index + 1}</td>
                <td style="padding: 12px; font-weight: 600; text-transform: uppercase;">
                    ${item.user_detail.fullName}
                </td>
                <td style="padding: 12px; text-align: center; font-family: monospace;">
                    ${item.user_bank[0]?.nomor_rekening || '-'}
                </td>
                <td style="padding: 12px;">
                    ${item.user_bank[0]?.bank?.short_name || '-'}
                </td>
                <td style="padding: 12px; text-align: right; font-weight: bold; color: #059669;">
                    ${formattedIndividu}
                </td>
                <td style="padding: 12px; text-align: center;">
                    <span style="padding: 2px 8px; border-radius: 10px; font-size: 10px; ${item.user_transaction?.length > 0 ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;'}">
                        ${item.user_transaction?.length > 0 ? 'Selesai' : 'Belum'}
                    </span>
                </td>
            </tr>
        `;
                }).join('');
                $(win.document.body)
                    .css('font-family', 'Poppins, sans-serif')
                    .prepend(`
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
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">SAMPAH</h2>
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

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                <thead>
                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">No</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Nama Lengkap</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: center;">Nomor Rekening</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Bank</th>
                         <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Total Saldo</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>

                <tfoot>
                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                        <th colspan="4" style="padding: 12px; border-top: 2px solid #f3f4f6; text-align: right;">Setoran yang belum dicairkan: </th>
                        <th style="padding: 12px; border-top: 2px solid #f3f4f6; text-align: left; font-weight: bold;">${formattedTotalSemua}</th>
                        <th style="padding: 12px; border-top: 2px solid #f3f4f6;"></th>
                    </tr>
                </tfoot>
            </table>

           <div style="display: flex; justify-content: flex-end; margin-top: 40px;">
                            <div style="text-align: center; width: 220px;">
                                <p style="font-size: 11px; margin-bottom: 60px;">Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}<br><b>Verifikator</b></p>
                                <div style="border-bottom: 1px solid #d1d5db; width: 180px; margin: 0 auto 5px;"></div>
                                <p style="font-weight: bold; font-size: 12px; text-transform: uppercase;">( Ketua Bank Sampah RT-0${page.props.auth.user.user_detail?.id_rt || '-'} )</p>
                                <p style="font-size: 9px; color: #9ca3af;">ID: SBK-RT0${page.props.auth.user.user_detail?.id_rt || '-'}</p>
                            </div>
                        </div>
        </div>
    `);

                // Sembunyikan tabel asli bawaan DataTables agar tidak dobel
                $(win.document.body).find('table').last().hide();
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


const dtOptions2 = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],


    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: null,
        bottomEnd: 'paging'
    },

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

const kirimBukti = (id) => {
    // Logika kirim bukti pembayaran
    console.log("Kirim bukti untuk ID:", id);
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Transaksi', url: route('data-transaksi') },
];

const sendReminder = ($id) => {
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

                message: `Bank Sampah RT0${props.IDRT} menyelesaikan pelaporan dan mengajukan pembukaan rekening pencairan setoran`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};


const initials = (fullName) => {
    if (!fullName) return '??';

    const name = fullName;
    const words = name.split(' ');

    const firstInitial = words[0]?.substring(0, 1) || '';
    const secondInitial = words[1]?.substring(0, 1) || '';

    return (firstInitial + secondInitial).toUpperCase();
};

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


const handleSubmit = () => {

    const url = route('bs.add-transaction');
    const method = 'post';

    form[method](url, {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire('Berhasil!', 'Data transaksi telah diproses.', 'success');
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
                Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
            }
        },

    });
};
</script>

<template>

    <Head title="Data Transaksi" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">

        <template v-if="user.user_detail.status_transaction === 'Belum Disetujui'">
            <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">

                <div class="flex flex-col gap-5 bg-gray-200 dark:bg-gray-800 transition-colors">

                    <template v-if="props.transaction.length === 0">

                        <h3
                            class="border-b capitalize border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                            Anda belum melakukan pencatatan setoran nasabah !!!
                        </h3>

                        <span class="w-full font-medium capitalize text-gray-700 dark:text-gray-300">
                            Lakukan pencatatan pada menu manajemen nasabah -> Pencatatan Setoran
                        </span>
                    </template>




                    <template v-else-if="props.reporting.length > 0">
                        <h3
                            class="border-b capitalize border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                            Anda belum melakukan pelaporan setoran ke RW !!!
                        </h3>

                        <span v-if="props.reporting.length > 0"
                            class="w-full font-medium capitalize text-gray-700 dark:text-gray-300">
                            Lakukan pengajuan pelaporan ke RW dengan menekan tombol reminder dibawah ini
                        </span>

                        <span v-else class="w-full font-medium capitalize text-gray-700 dark:text-gray-300">
                            Lakukan pelaporan dengan upload dokumen hasil setoran atau foto bukti pelaksanaan kegiatan
                            melalui menu manajemen nasabah -> Pelaporan setoran
                        </span>
                    </template>

                    <template v-else>
                        <h3
                            class="border-b border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                            Anda belum melakukan verifikasi akun !!!
                        </h3>

                        <span class="w-full font-medium text-gray-700 dark:text-gray-300">
                            Isi Biodata anda dan keperluan dokumen (Opsional)
                        </span>
                    </template>

                    <template v-if="props.transaction.length === 0">
                        <button @click=""
                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                            <i class="fas fa-bell"></i> Anda Belum Melakukan Pencatatan Setoran
                        </button>


                    </template>

                    <template v-else-if="props.reporting.length > 0">
                        <button @click="sendReminder(props.IDRW)"
                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                            <i class="fas fa-bell"></i> Lakukan Pengajuan Persetujuan Buka Rekening Ke RW
                        </button>


                    </template>


                </div>

            </div>


        </template>
        <template v-else>
            <div class="grid gap-4">

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap justify-between items-center gap-4 " :class="[
                        showForm ? 'mb-4' : 'mb-0'
                    ]">
                        <h3 class="text-lg font-bold text-black dark:text-white">Pencairan Dana Nasabah</h3>
                        <button @click="showForm = !showForm"
                    class=" text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95"
                    :class="[
                        showForm ? 'bg-red-500 hover:bg-red-600 shadow-red-500/20' : 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20'
                    ]">

                    <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                    {{ showForm ? 'Tutup Form' : 'Tambah Transaksi' }}
                </button>
                    </div>

                    <Transition name="accordion">
                        <div v-if="showForm"
                            class="bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-black dark:text-white">
                                {{ isEdit ? 'Perbarui Data' : 'Input Data Baru' }}
                            </h3>



                            <FormWrapper :errors="form.errors" :processing="form.processing" @submit="handleSubmit">

                                <input type="hidden" name="id_userdetail" v-model="form.id_userdetail">

                                <div class="grid grid-cols-1 gap-4">



                                    <template v-for="field in formdata.Dokumen" :key="field.name">



                                        <div v-if="field.type === 'file' && field.name === 'fileDoc'"
                                            class="flex flex-col">
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
                                            <div v-if="form.errors[field.name]" class="text-red-500 text-xs mt-1">{{
                                                form.errors[field.name] }}</div>
                                        </div>

                                    </template>
                                </div>

                                <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                                    <button type="submit"
                                        class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                                        :disabled="form.processing">
                                        <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Dokumen' : 'Simpan Dokumen'
                                        }}
                                    </button>
                                </div>

                            </FormWrapper>

                        </div>
                    </Transition>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-7 gap-4">

                    <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-xl shadow p-5 overflow-hidden">
                        <h3 class="mb-4 font-bold text-gray-500 dark:text-white text-sm uppercase tracking-wider">
                            Riwayat Transaksi</h3>
                        <div class="overflow-x-auto">

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
                                        <input @keyup="handleSearch" type="text"
                                            class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-30 transition-all"
                                            placeholder="Ketik...">
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <label
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                                        <select @change="handleCategoryFilter"
                                            class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                                            <option value="">Semua</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Belum Dibayar">Belum Dibayar</option>
                                        </select>
                                    </div>

                                    <div class="flex items-center gap-2">
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
                            <DataTable ref="dtInstance" :data="nasabah" :options="dtOptions"
                                class="w-full stripe hover">
                                <thead>
                                    <tr>
                                        <th class="text-black dark:text-white capitalize">No</th>
                                        <th class="text-black dark:text-white capitalize">Nasabah</th>
                                        <th class="text-black dark:text-white capitalize">Nomor Rekening</th>
                                        <th class="text-black dark:text-white capitalize">Bank</th>
                                        <th class="text-black dark:text-white capitalize">Total Saldo</th>
                                        <th class="text-black dark:text-white capitalize">Status</th>
                                        <th class="text-black dark:text-white capitalize">Aksi</th>
                                    </tr>
                                </thead>


                            </DataTable>
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-gray-50 dark:bg-gray-700 rounded-xl shadow p-5">
                        <h3
                            class="mb-4 font-bold text-center border-b dark:border-gray-600 pb-2 dark:text-white text-black text-sm uppercase">
                            Pilih Nasabah</h3>
                        <div class="overflow-x-auto">
                            <DataTable :options="dtOptions2" class="w-full text-xs">
                                <thead>
                                    <tr class="text-left border-b dark:border-gray-600">
                                        <th class="pb-2 text-black dark:text-white">Profil</th>
                                        <th class="pb-2 text-black dark:text-white">Nama</th>
                                        <th class="pb-2 text-black dark:text-white">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in props.nasabahAll" :key="user.id"
                                        @click="viewDetail(user.user_detail.id_user)"
                                        class="cursor-pointer hover:bg-emerald-50 text-black dark:text-white dark:hover:bg-gray-600 transition border-b dark:border-gray-600 last:border-0">
                                        <td class="py-2">


                                            <div class="border-gray-100 w-max dark:border-gray-800">
                                                <div v-if="user"
                                                    class="profile-circle py-1 px-2  rounded-full border border-gray-600 text-gray-800 dark:text-white">
                                                    {{ initials(user.user_detail?.fullName) }}
                                                </div>

                                                <div v-else class="profile-circle">
                                                    <img class="w-8 h-8 rounded-full"
                                                        src="https://ui-avatars.com/api/?name=Guest&background=random"
                                                        alt="Guest">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2 font-medium text-black dark:text-white">{{
                                            user.user_detail.fullName }}</td>
                                        <td class="py-2 text-right">
                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </DataTable>
                        </div>
                    </div>

                </div>
            </div>
        </template>

    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

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
    color: #6b7280 !important;
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
