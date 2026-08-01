<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import { computed, nextTick, ref, watch, onMounted } from 'vue'

// ================= DATATABLES =================
import DataTablesCore from 'datatables.net'
import Buttons from 'datatables.net-buttons'
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5'
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print'
import Responsive from 'datatables.net-responsive-dt'
import DataTable from 'datatables.net-vue3'
import XLSX from 'xlsx-js-style';

// CSS (WAJIB)
import 'datatables.net-dt/css/dataTables.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)

// ================= PROPS =================
const props = defineProps({
    jadwalPelaksanaan: Array,
    nasabahList: Array,
    jenisSampah: Array,
    sidebardata: Object,
    pencatatanSetoranItems: Array,
    pencatatanSetoran: Array
})

// State untuk Step Form
const step = ref(1);
const itemsPerStep = 8;
const showForm = ref(false);

const isEdit = ref(false);

// Inisialisasi Form dengan useForm
const form = useForm({
    id_jadwal: '',
    id_userdetail: '',
    // Kita buat array of objects untuk berat sampah sesuai id_sampah
    items: props.jenisSampah.map(s => ({
        sampah_id: s.id,
        nama: s.nama_sampah,
        satuan: s.satuan,
        harga_satuan: s.harga,
        kategori: s.kategori,
        saldo: s.saldo,
        jumlah: 0

    }))
});

const filteredJadwal = computed(() => {

    const semuaJadwal = props.jadwalPelaksanaan || [];
    const itemsTercatat = props.pencatatanSetoran || [];

    if (!form.id_userdetail) return [];

    const idJadwalTerpakai = itemsTercatat
        .filter(item =>
            Number(item.id_userdetail) === Number(form.id_userdetail)
        )
        .map(item => Number(item.id_jadwal));

    return semuaJadwal.filter(j =>
        !idJadwalTerpakai.includes(Number(j.id))
    );
});



// SESUDAH
watch(() => form.id_userdetail, () => {
    form.id_jadwal = "";
});
// Membagi data sampah menjadi per-step (seperti chunk di Blade)
const chunks = computed(() => {
    const result = [];
    for (let i = 0; i < form.items.length; i += itemsPerStep) {
        result.push(form.items.slice(i, i + itemsPerStep));
    }
    return result;
});

const totalSteps = computed(() => chunks.value.length);


const editData = (item) => {
    isEdit.value = true;
    form.id = item.id;
    form.nama_sampah = item.nama_sampah;
    form.satuan = item.satuan;
    form.harga = item.harga;
    form.kategori = item.kategori;
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleSubmit = () => {
    const url = isEdit.value ? route('update-sampah', form.id) : route('add-setoran');
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            isEdit.value ?
                Swal.fire('Berhasil!', 'Data nasabah telah diubah.', 'success') : Swal.fire('Berhasil!', 'Setoran telah disimpan.', 'success');
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
                Swal.fire('Error', xhr.responseJSON?.message || 'Maaf, Inputan Anda ada yang salah, silahkan cek kembali', 'error');
            }
        },

    });
};

const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});



const filteredJenisSampah = computed(() => {
    // Pastikan data ada sebelum di-filter
    const data = props.jenisSampah || [];

    return data.filter(s => {
        // Bandingkan kategori, pastikan handle null dengan optional chaining
        return s.kategori?.trim() === activeCategory.value.trim();
    });
});

// ================= FILTER STATE =================
const selectedYear = ref(new Date().getFullYear());
const years = [2024, 2025, 2026];
const selectedBulan = ref('');
const selectedSampah = ref('');
const activeCategory = ref('Non Daur Ulang');
const categories = ['Non Daur Ulang', 'Daur Ulang', 'Lainnya'];
const selectedJadwalFilter = ref('');

const months = [
    { id: 1, name: 'Jan' }, { id: 2, name: 'Feb' }, { id: 3, name: 'Mar' },
    { id: 4, name: 'Apr' }, { id: 5, name: 'Mei' }, { id: 6, name: 'Jun' },
    { id: 7, name: 'Jul' }, { id: 8, name: 'Agu' }, { id: 9, name: 'Sep' },
    { id: 10, name: 'Okt' }, { id: 11, name: 'Nov' }, { id: 12, name: 'Des' }
];

// Sampah yang sesuai kategori aktif
const filteredSampahByKategori = computed(() =>
    (props.jenisSampah || []).filter(s => s.kategori?.trim() === activeCategory.value.trim())
);

const processedData = computed(() => {
    if (!props.nasabahList) return [];

    return props.nasabahList.map(nasabah => {
        let total = 0;

        (nasabah.pencatatan || []).forEach(nota => {
            const tgl = new Date(nota.created_at);

            const matchYear = tgl.getFullYear() === selectedYear.value;

            const matchJadwal = !selectedJadwalFilter.value ||
                Number(nota.id_jadwal) === Number(selectedJadwalFilter.value);

            const matchBulan =
                viewMode.value === 'sampah'
                    ? (!selectedBulan.value || (tgl.getMonth() + 1) === Number(selectedBulan.value))
                    : (!selectedBulan.value || (tgl.getMonth() + 1) === Number(selectedBulan.value));



            if (matchYear && matchJadwal && matchBulan) {
                (nota.pencatatan_items || []).forEach(item => {

                    if (viewMode.value === 'sampah') {
                        const info = props.jenisSampah.find(s => s.id === item.sampah_id);

                        const matchKategori = info?.kategori?.trim() === activeCategory.value.trim();
                        const matchSampah = !selectedSampah.value || item.sampah_id === Number(selectedSampah.value);

                        if (matchKategori && matchSampah) {
                            total += parseFloat(item.jumlah || 0);
                        }
                    } else {
                        total += parseFloat(item.subtotal || 0);
                    }

                });
            }
        });

        return { ...nasabah, totalTahunan: total };
    });
});

// ================= KOLOM DINAMIS =================
// Jika bulan dipilih → kolom per jenis sampah
// Jika bulan kosong → kolom per bulan

const isModeSampah = computed(() => !!selectedBulan.value);

const viewMode = ref('saldo');
const dynamicColumns = computed(() => {

    // 🔥 PRIORITAS 1: JIKA PILIH JADWAL → HANYA 1 KOLOM
    if (selectedJadwalFilter.value) {

        const jadwal = props.jadwalPelaksanaan.find(
            j => Number(j.id) === Number(selectedJadwalFilter.value)
        );

        if (!jadwal) return [];

        const tanggal = new Date(jadwal.tanggal_setoran);
        const label = tanggal.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short'
        });

        return [{
            title: label.toUpperCase(),
            data: null,
            className: 'text-center col-bulan',

            render: (data, type, row) => {
                let total = 0;

                row.pencatatan?.forEach(nota => {
                    if (
                        Number(nota.id_jadwal) === Number(selectedJadwalFilter.value)
                    ) {
                        nota.pencatatan_items?.forEach(item => {
                            total += viewMode.value === 'sampah'
                                ? parseFloat(item.jumlah || 0)
                                : parseFloat(item.subtotal || 0);
                        });
                    }
                });

                return type === 'display'
                    ? total.toLocaleString('id-ID')
                    : total;
            }
        }];
    }

    // 🔥 PRIORITAS 2: MODE SAMPAH
    if (viewMode.value === 'sampah') {
        let sampahList = filteredSampahByKategori.value;

        if (selectedSampah.value) {
            sampahList = sampahList.filter(s => s.id === Number(selectedSampah.value));
        }

        return sampahList.map(s => ({
            title: s.nama_sampah.toUpperCase(),
            data: null,
            className: 'text-center col-bulan',
            render: (data, type, row) => {
                let total = 0;

                row.pencatatan?.forEach(nota => {
                    const tgl = new Date(nota.jadwal.tanggal_setoran);

                    if (
                        tgl.getFullYear() === selectedYear.value &&
                        (!selectedBulan.value || (tgl.getMonth() + 1) === Number(selectedBulan.value))
                    ) {
                        nota.pencatatan_items?.forEach(item => {
                            if (item.sampah_id === s.id) {
                                total += parseFloat(item.jumlah || 0);
                            }
                        });
                    }
                });

                return type === 'display'
                    ? total.toLocaleString('id-ID')
                    : total;
            }
        }));
    }

    // 🔥 PRIORITAS 3: DEFAULT (BULAN)
    const bulanList = selectedBulan.value
        ? months.filter(m => m.id === Number(selectedBulan.value))
        : months;

    return bulanList.map(m => ({
        title: m.name.toUpperCase(),
        data: null,
        className: 'text-center col-bulan',
        render: (data, type, row) => {
            let total = 0;

            row.pencatatan?.forEach(nota => {
                const tgl = new Date(nota.jadwal.tanggal_setoran);

                if (
                    tgl.getFullYear() === selectedYear.value &&
                    (tgl.getMonth() + 1) === m.id
                ) {
                    nota.pencatatan_items?.forEach(item => {
                        total += parseFloat(item.subtotal || 0);
                    });
                }
            });

            return type === 'display'
                ? total.toLocaleString('id-ID')
                : total;
        }
    }));
});

const mobileSearch = ref('');
const mobilePage = ref(1);
const mobilePerPage = ref(5);

const filteredMobileData = computed(() => {
    let data = processedData.value;

    if (mobileSearch.value) {
        const keyword = mobileSearch.value.toLowerCase();

        data = data.filter(row =>
            row.fullName.toLowerCase().includes(keyword)
        );
    }

    return data;
});

const paginatedMobileData = computed(() => {
    const start = (mobilePage.value - 1) * mobilePerPage.value;
    const end = start + mobilePerPage.value;

    return filteredMobileData.value.slice(start, end);
});

const totalMobilePages = computed(() => {
    return Math.ceil(filteredMobileData.value.length / mobilePerPage.value);
});

const nextMobilePage = () => {
    if (mobilePage.value < totalMobilePages.value) {
        mobilePage.value++;
    }
};

const prevMobilePage = () => {
    if (mobilePage.value > 1) {
        mobilePage.value--;
    }
};

watch(selectedJadwalFilter, (val) => {
    if (val) {
        selectedBulan.value = ''; // reset bulan
        selectedSampah.value = '';
    }
});

const pencatatan = props.pencatatanSetoranItems.find(p =>
    Number(p.id_userdetail) === Number(row.id) &&
    Number(p.id_jadwal) === Number(selectedJadwalFilter.value)
);

// ================= DT OPTIONS =================
const dtOptions = computed(() => ({
    scrollX: false,
    autoWidth: true,
    pageLength: 5,
    responsive: {
        details: {
            type: 'column',
            target: 0
        }
    },
    lengthMenu: [5, 10, 25, 50],
    columns: [

        {
            data: 'fullName',
            title: 'NASABAH',
            className: 'font-bold text-gray-700 dark:text-white uppercase'
        },
        ...dynamicColumns.value,
        {
            title: 'TOTAL',
            data: 'totalTahunan',
            className: 'text-right font-black text-emerald-600 bg-emerald-50/50 dark:bg-gray-900 border-l col-total',
            render: (data, type) => type === 'display' ? data.toLocaleString('id-ID') : data
        },
        {
            title: 'DETAIL',
            data: null,
            orderable: false,
            className: 'text-center no-print',
            render: (data, type, row) => {

                if (selectedJadwalFilter.value) {

                    const pencatatan = props.pencatatanSetoranItems.find(p =>
                        Number(p.id_userdetail) === Number(row.id) &&
                        Number(p.id_jadwal) === Number(selectedJadwalFilter.value)
                    );

                    const pencatatanId = pencatatan?.id ?? '';

                    return `<button @click="viewDetail(${row.id}, ${selectedJadwalFilter.value})" class=" bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm"
            data-id="${row.id}"
            data-pencatatan="${pencatatanId}"
            data-jadwal="${selectedJadwalFilter.value}">
            <i class="fas fa-eye"></i>
        </button>`;
                }

                return `<button class="btn-detail bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm"
        data-id="${row.id}">
        <i class="fas fa-eye"></i>
    </button>`;
            }
        }
    ],
    buttons: [
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            pageSize: 'A4',
            // Otomatis Landscape jika kolom terlalu banyak
            orientation: dynamicColumns.value.length > 5 ? 'landscape' : 'portrait',
            title: 'Laporan Sampah SiBanksa RT' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),

            exportOptions: {
                columns: ':not(.no-print)'
            },
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses PDF...',
                    text: `Menyiapkan layout laporan digital`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (doc) {
                    Swal.close();

                    // 1. Pengaturan Tabel
                    const tableNode = doc.content.find(c => c.table);
                    if (tableNode) {
                        // Set widths: Kolom 1 (No/Nama) agak lebar, sisanya bagi rata (*)
                        const colCount = tableNode.table.body[0].length;

                        // 💡 TRICK 1: Perkecil font otomatis jika kolom lebih dari 7 agar tidak terpotong
                        if (colCount > 7) {
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 8;
                        }

                        // 💡 TRICK 2: Gunakan 'auto' untuk kolom data, dan '*' untuk nama nasabah
                        // Ini memaksa kolom angka mengecil sesuai isinya, dan sisa ruang diberikan ke nama
                        let widths = Array(colCount).fill('auto');
                        widths[0] = '*'; // Kolom Nama Nasabah fleksibel
                        tableNode.table.widths = widths;

                        tableNode.table.dontBreakRows = true;
                        // Memaksa tabel untuk tidak melebihi lebar halaman (A4)
                        tableNode.table.keepWithHeaderRows = 1;
                        // 💡 TRICK 3: Hapus margin kiri kanan dokumen agar tabel punya ruang lebih luas
                        doc.pageMargins = [20, 20, 20, 20];

                        tableNode.table.body.forEach((row, rowIndex) => {
                            row.forEach((cell, i) => {
                                if (!row[i]) row[i] = { text: '' };

                                // Alignment angka agar rapi
                                if (rowIndex > 0 && i > 0) {
                                    row[i].alignment = 'center';
                                }

                                // Capitalize text
                                if (rowIndex > 0 && i === 0) {
                                    let txt = row[i].text || row[i].toString();
                                    row[i].text = txt.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                }
                            });
                        });

                        tableNode.layout = {
                            hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 1 : 0.5,
                            vLineWidth: () => 0.5,
                            hLineColor: () => '#e2e8f0',
                            vLineColor: () => '#e2e8f0',
                            paddingLeft: () => 8,
                            paddingRight: () => 8,
                        };
                    }

                    // 2. Custom Header (SiBanksa Brand)
                    const userDetail = page.props.auth.user?.user_detail;
                    doc.content.splice(0, 1,
                        {
                            columns: [
                                {
                                    stack: [
                                        { text: 'SiBanksa', fontSize: 22, bold: true, color: '#10b981' },
                                        { text: 'Sistem Informasi Bank Sampah Digital', fontSize: 8, color: '#6b7280', letterSpacing: 1 }
                                    ]
                                },
                                {
                                    stack: [
                                        { text: 'LAPORAN PENYETORAN', fontSize: 16, bold: true, alignment: 'right' },
                                        { text: `UNIT RT-0${userDetail?.id_rt || '-'} / RW-01`, fontSize: 10, alignment: 'right', color: '#9ca3af' },
                                        { text: `Tahun: ${selectedYear.value}`, fontSize: 9, alignment: 'right' }
                                    ],
                                    width: '*'
                                }
                            ]
                        },
                        {
                            canvas: [{ type: 'line', x1: 0, y1: 5, x2: doc.internal?.pageSize?.width - 80 || 515, y2: 5, lineWidth: 2, lineColor: '#10b981' }],
                            margin: [0, 5, 0, 20]
                        }
                    );

                    // 3. Footer (Tanda Tangan)
                    doc.content.push(
                        { text: '\n\n' },
                        {
                            columns: [
                                { text: '', width: '*' },
                                {
                                    width: 200,
                                    stack: [
                                        { text: `Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`, alignment: 'center', fontSize: 10 },
                                        { text: 'Ketua Unit Bank Sampah', alignment: 'center', margin: [0, 5, 0, 50], fontSize: 10 },
                                        { text: `KETUA ${userDetail?.fullName?.toUpperCase() || '..........................'} )`, alignment: 'center', bold: true, fontSize: 11 },
                                        { canvas: [{ type: 'line', x1: 20, y1: 2, x2: 180, y2: 2, lineWidth: 0.5 }] },
                                        { text: 'ID: SBK-RT0' + userDetail?.id_rt, alignment: 'center', fontSize: 8, color: '#9ca3af' }
                                    ]
                                }
                            ]
                        }
                    );

                    // 4. Global Styles
                    doc.styles.tableHeader = {
                        fillColor: '#10b981',
                        color: 'white',
                        bold: true,
                        alignment: 'center',
                        fontSize: 10,
                        margin: [0, 5, 0, 5]
                    };
                    doc.defaultStyle = {
                        fontSize: 9,
                        color: '#374151'
                    };
                }

                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
                }, 300);
            },
        },

        // ================= EXCEL (STYLISH & STRUCTURED) =================
        {
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',

            action: function (e, dt) {

                Swal.fire({
                    title: 'Mereset & Merakit Excel...',
                    text: 'Memberikan border dan styling...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const data = dt.rows({ search: 'applied' }).data().toArray();
                const wb = XLSX.utils.book_new();

                // ================= MAIN SHEET =================
                const excelRows = data.map((row, idx) => {
                    const res = {
                        "NO": idx + 1,
                        "NAMA NASABAH": row.fullName || '-',
                    };

                    dynamicColumns.value.forEach(col => {
                        res[col.title] = col.render(null, 'sort', row) || 0;
                    });

                    res["TOTAL KESELURUHAN"] = row.totalTahunan || 0;
                    return res;
                });

                const ws = XLSX.utils.json_to_sheet(excelRows, { origin: 'A6' });

                // ================= HEADER =================
                XLSX.utils.sheet_add_aoa(ws, [
                    ["LAPORAN BANK SAMPAH SIBANKSA"],
                    [`UNIT RT-0${userDetail.value?.id_rt || '-'}`],
                    [`TAHUN ${selectedYear.value}`],
                    [`Dicetak: ${new Date().toLocaleString('id-ID')}`],
                    []
                ], { origin: 'A1' });

                const colCount = Object.keys(excelRows[0]).length;

                // ================= MERGE =================
                ws['!merges'] = [
                    { s: { r: 0, c: 0 }, e: { r: 0, c: colCount - 1 } },
                    { s: { r: 1, c: 0 }, e: { r: 1, c: colCount - 1 } },
                    { s: { r: 2, c: 0 }, e: { r: 2, c: colCount - 1 } },
                    { s: { r: 3, c: 0 }, e: { r: 3, c: colCount - 1 } },
                ];

                // ================= STYLE GLOBAL =================
                const range = XLSX.utils.decode_range(ws['!ref']);

                for (let R = range.s.r; R <= range.e.r; ++R) {
                    for (let C = range.s.c; C <= range.e.c; ++C) {

                        const cell = XLSX.utils.encode_cell({ r: R, c: C });
                        if (!ws[cell]) continue;

                        ws[cell].s = {
                            border: {
                                top: { style: "thin" },
                                bottom: { style: "thin" },
                                left: { style: "thin" },
                                right: { style: "thin" }
                            },
                            alignment: {
                                vertical: "center",
                                horizontal: C === 1 ? "left" : "center"
                            }
                        };

                        // HEADER TITLE
                        if (R === 0) {
                            ws[cell].s.font = { bold: true, sz: 14 };
                            ws[cell].s.alignment = { horizontal: "center" };
                        }

                        // SUB HEADER
                        if (R >= 1 && R <= 3) {
                            ws[cell].s.font = { bold: true };
                            ws[cell].s.alignment = { horizontal: "center" };
                        }

                        // HEADER TABLE
                        if (R === 5) {
                            ws[cell].s.font = { bold: true, color: { rgb: "FFFFFF" } };
                            ws[cell].s.fill = { fgColor: { rgb: "10B981" } };
                            ws[cell].s.alignment = { horizontal: "center" };
                        }
                    }
                }

                ws['!cols'] = Object.keys(excelRows[0]).map(() => ({ wch: 20 }));

                XLSX.utils.book_append_sheet(wb, ws, 'LAPORAN UTAMA');

                months.forEach(m => {
                    const sheetData = data.map(row => {
                        let totalBulanIni = 0;

                        // 1. Inisialisasi baris nasabah
                        const res = {
                            "NASABAH": row.fullName || '-'
                        };

                        // 2. Tentukan Kolom Dinamis berdasarkan ViewMode
                        if (viewMode.value === 'sampah') {
                            // Jika mode sampah, buat kolom: [Plastik, Kertas, Logam, dst]
                            filteredSampahByKategori.value.forEach(s => {
                                res[s.nama_sampah.toUpperCase()] = 0;
                            });
                        } else {
                            // Jika mode saldo, hanya ada satu kolom nilai untuk bulan tersebut
                            res[`SETORAN ${m.name.toUpperCase()}`] = 0;
                        }

                        // 3. Isi Data dari Pencatatan
                        row.pencatatan?.forEach(nota => {
                            const tgl = new Date(nota.created_at);

                            // Filter Tahun, Bulan, dan Jadwal
                            if (
                                Number(tgl.getFullYear()) === Number(selectedYear.value) &&
                                Number(tgl.getMonth() + 1) === Number(m.id) &&
                                (!selectedJadwalFilter.value || Number(nota.id_jadwal) === Number(selectedJadwalFilter.value))
                            ) {
                                nota.pencatatan_items?.forEach(item => {
                                    const nilai = viewMode.value === 'sampah'
                                        ? parseFloat(item.jumlah || 0)  // Satuan (Kg/Pcs)
                                        : parseFloat(item.subtotal || 0); // Rupiah

                                    if (viewMode.value === 'sampah') {
                                        const info = props.jenisSampah.find(s => s.id === item.sampah_id);
                                        if (info && res[info.nama_sampah.toUpperCase()] !== undefined) {
                                            res[info.nama_sampah.toUpperCase()] += nilai;
                                        }
                                    } else {
                                        res[`SETORAN ${m.name.toUpperCase()}`] += nilai;
                                    }
                                    totalBulanIni += nilai;
                                });
                            }
                        });

                        res["TOTAL AKHIR"] = totalBulanIni;
                        return res;
                    });

                    // 4. Hanya buat sheet jika ada transaksi (TOTAL > 0)
                    if (!sheetData.some(r => r["TOTAL AKHIR"] > 0)) return;

                    // 5. Buat Worksheet & Tambahkan Header
                    const wsMonth = XLSX.utils.json_to_sheet(sheetData, { origin: 'A6' });

                    const judulLaporan = viewMode.value === 'sampah'
                        ? `LAPORAN DETAIL SAMPAH - ${m.name.toUpperCase()} ${selectedYear.value}`
                        : `LAPORAN SALDO NASABAH - ${m.name.toUpperCase()} ${selectedYear.value}`;

                    XLSX.utils.sheet_add_aoa(wsMonth, [
                        [judulLaporan],
                        [`UNIT RT-0${userDetail.value?.id_rt || '-'} / RW-01`],
                        [`Tipe Laporan: ${viewMode.value.toUpperCase()}`],
                        [`Dicetak: ${new Date().toLocaleString('id-ID')}`],
                        []
                    ], { origin: 'A1' });

                    // 6. Styling & Merge (Gunakan logic yang sudah Anda miliki sebelumnya)
                    const colCount = Object.keys(sheetData[0]).length;
                    wsMonth['!merges'] = [
                        { s: { r: 0, c: 0 }, e: { r: 0, c: colCount - 1 } },
                        { s: { r: 1, c: 0 }, e: { r: 1, c: colCount - 1 } },
                        { s: { r: 2, c: 0 }, e: { r: 2, c: colCount - 1 } },
                        { s: { r: 3, c: 0 }, e: { r: 3, c: colCount - 1 } },
                    ];

                    // Berikan border dan warna hijau pada header (Baris 6 / Index 5)
                    const range = XLSX.utils.decode_range(wsMonth['!ref']);
                    for (let R = range.s.r; R <= range.e.r; ++R) {
                        for (let C = range.s.c; C <= range.e.c; ++C) {
                            const cell = XLSX.utils.encode_cell({ r: R, c: C });
                            if (!wsMonth[cell]) continue;

                            wsMonth[cell].s = {
                                border: { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } },
                                alignment: { vertical: "center", horizontal: C === 0 ? "left" : "center" }
                            };

                            if (R === 5) { // Header Tabel
                                wsMonth[cell].s.font = { bold: true, color: { rgb: "FFFFFF" } };
                                wsMonth[cell].s.fill = { fgColor: { rgb: "10B981" } };
                            }
                        }
                    }

                    wsMonth['!cols'] = Object.keys(sheetData[0]).map((k, i) => ({ wch: i === 0 ? 30 : 20 }));

                    XLSX.utils.book_append_sheet(wb, wsMonth, `${m.name.toUpperCase()} ${selectedYear.value}`);
                });

                XLSX.writeFile(wb, `Laporan Nasabah SiBanksa RT-0${page.props.auth.user.user_detail?.id_rt || '-'} Tanggal ${new Date().toLocaleDateString('id-ID').replace(/\//g, '-')}.xlsx`);

                Swal.close();
            }
        },

        // ================= PRINT (FULL CUSTOM) =================
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',

            exportOptions: {
                columns: ':not(.no-print)'
            },

            action: function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses Print...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (win) {
                    Swal.close();

                    const data = dt.rows({ search: 'applied' }).data().toArray();
                    const user = userDetail.value;

                    // ================= HEADER DINAMIS =================
                    const tableHeader = `
                    <tr style="background:#f9fafb;font-size:10px;text-transform:uppercase;">
                        <th style="padding:10px;">No</th>
                        <th style="padding:10px;">Nasabah</th>

                        ${dynamicColumns.value.map(col => `
                            <th style="padding:10px;text-align:center;">
                                ${col.title}
                            </th>
                        `).join('')}

                        <th style="padding:10px;">Total</th>
                    </tr>
                `;

                    // ================= ROW DINAMIS =================
                    const tableRows = data.map((row, index) => `
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;">${index + 1}</td>
                        <td style="padding:10px;">${row.fullName}</td>

                        ${dynamicColumns.value.map(col => `
                            <td style="padding:10px;text-align:center;">
                                ${col.render(null, 'display', row)}
                            </td>
                        `).join('')}

                        <td style="padding:10px;font-weight:bold;">
                            ${row.totalTahunan.toLocaleString('id-ID')}
                        </td>
                    </tr>
                `).join('');

                    // ================= RENDER =================
                    $(win.document.body)
                        .css('font-family', 'Poppins, sans-serif')
                        .html(`
                        <div style="padding: 40px; border-top: 10px solid #10b981; background: white;">

                            <!-- HEADER -->

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

                            <!-- TABLE -->
                                      <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                                <thead>${tableHeader}</thead>
                                <tbody>${tableRows}</tbody>
                            </table>

                            <!-- FOOTER -->
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
                };

                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);
                }, 300);
            }
        }
    ],
    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
    footerCallback: function () {
        const api = this.api();

        // Hitung footer tiap kolom dinamis
        dynamicColumns.value.forEach((col, idx) => {
            const colIdx = idx + 1; // offset kolom Nasabah
            let sum = 0;
            api.rows({ page: 'current' }).data().each(row => {
                sum += col.render(null, 'sort', row) || 0;
            });
            $(api.column(colIdx).footer()).html(sum > 0 ? sum.toLocaleString('id-ID') : '0');
        });

        // Footer kolom TOTAL
        let grandTotal = 0;
        api.rows({ page: 'current' }).data().each(row => {
            grandTotal += parseFloat(row.totalTahunan || 0);
        });
        const lastColIdx = dynamicColumns.value.length + 1;
        $(api.column(lastColIdx).footer()).html(grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0');
    },
    language: {
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
            previous: "← Sebelumnya",
            next: "Berikutnya →"
        },
        emptyTable: "Tidak ada data tersedia"
    }
}));

window.viewDetail = (id, idJadwal) => viewDetail(id, idJadwal);

const viewDetail = (userId, idJadwal) => {
    // Validasi sederhana agar tidak error lagi
    if (!userId || !idJadwal) {
        console.error("Gagal navigasi: Parameter tidak lengkap", { userId, idJadwal });
        return;
    }

    router.get(route('show-pencatatanByBulan', {
        id: userId,
        idJadwal: idJadwal  // Pastikan kunci ini sesuai dengan {idJadwal} di web.php
    }));
};


const isMobile = ref(window.innerWidth < 768);

window.addEventListener('resize', () => {
    isMobile.value = window.innerWidth < 768;
});
// Referensi instance tabel
const dtInstance = ref(null);
const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw();
};
const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const prevPage = () => dtInstance.value.dt.page('previous').draw('page');
const nextPage = () => dtInstance.value.dt.page('next').draw('page');

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


const openCreateForm = () => {
    isEdit.value = false;
    form.reset();
    showForm.value = !showForm.value;

};

const deleteData = (id) => {
    Swal.fire({
        title: 'Hapus data?',
        text: "Data setoran akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!'
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('', id));
        }
    });
};


onMounted(() => {
    $(document).on('click', '.btn-detail', function () {
        const id = $(this).data('id');

        // contoh: redirect ke detail
        router.visit(route('show-pencatatan', id));

        // ATAU modal:
        // openModalDetail(id)
    });

    $(document).on('click', '.btn-detailbyBulan', function () {
        const nasabahId = $(this).data('id');
        const jadwalId = $(this).data('jadwal');
        const pencatatanId = $(this).data('pencatatan');

        console.log('Nasabah ID:', nasabahId);
        console.log('Jadwal ID:', jadwalId);
        console.log('Pencatatan ID:', pencatatanId);

        // Redirect pakai pencatatanId (lebih spesifik)
        router.visit(route('show-pencatatanByBulan', {
            id: nasabahId,
            idJadwal: jadwalId
        }));
    });
});

const tableRows = computed(() => {
    if (!dtInstance.value) return '';

    return dtInstance.value.dt.rows({ search: 'applied' }).data().toArray().map((row, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${row.fullName}</td>
            ${dynamicColumns.value.map(col => `
                <td>${col.render(null, 'display', row)}</td>
            `).join('')}
            <td>${row.totalTahunan.toLocaleString('id-ID')}</td>
        </tr>
    `).join('');
});


const goToDetail = (id) => {
    router.visit(route('show-pencatatan', id));
};

const formatDate = (dateString) => {
    if (!dateString) return '-';

    const date = new Date(dateString);

    // Mengecek apakah date valid untuk menghindari 'Invalid Date'
    if (isNaN(date.getTime())) return dateString;

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Penyetoran Sampah', url: route('pencatatan-setoran') },
];
</script>

<template>

    <Head title="Pencatatan Setoran" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Penyetoran Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola pencatatan setoran nasabah anda.</p>
                </div>
                <button @click="openCreateForm"
                    class=" text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95"
                    :class="[
                        showForm ? 'bg-red-500 hover:bg-red-600 shadow-red-500/20' : 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20'
                    ]">

                    <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                    {{ showForm ? 'Tutup Form' : 'Tambah Setoran' }}
                </button>
            </div>


            <Transition name="accordion">
                <div v-if="showForm" class="p-5 bg-gray-50 dark:bg-gray-900">
                    <div class="bg-white accordion-wrapper dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                        <FormWrapper formName="formPencatatan" :errors="form.errors" :processing="form.processing"
                            @submit="handleSubmit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                                                    <InputLabel :for="'Nasabah'" :value="'Nasabah'" />
                                    <select v-model="form.id_userdetail"
                                             class="w-full  dark:border-gray-600 bg-white text-black  rounded-xl p-2.5 dark:bg-gray-900 dark:text-white focus:ring-emerald-500 border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['id_userdetail'] }">
                                        <option value="" disabled>Pilih Nasabah</option>
                                        <option v-for="n in nasabahList" :key="n.id" :value="n.id">
                                            {{ n.fullName }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                                                    <InputLabel :for="'Jadwal Pelaksanaan'" :value="'Jadwal Pelaksanaan'" />

                                    <select v-model="form.id_jadwal" :key="form.id_userdetail"
                                                                                     class="w-full  dark:border-gray-600 bg-white text-black  rounded-xl p-2.5 dark:bg-gray-900 dark:text-white focus:ring-emerald-500 border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                                                                      :disabled="!form.id_userdetail"
                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['id_jadwal'] }">

                                        <option value="" disabled>
                                            {{ !form.id_userdetail ? 'Pilih Nasabah dulu' : 'Pilih Jadwal' }}
                                        </option>

                                        <option v-for="j in filteredJadwal" :key="j.id" :value="j.id">
                                            {{ formatDate(j.tanggal_setoran) }}
                                        </option>
                                    </select>

                                    <p v-if="form.id_userdetail && filteredJadwal.length === 0"
                                        class="text-[10px] text-amber-600 mt-1 italic">
                                        * Semua jadwal untuk nasabah ini sudah tercatat.
                                    </p>
                                </div>

                            </div>


                            <div v-if="chunks.length === 0" class="text-center text-gray-500 py-10">
                                <i class="fas fa-box
-open text-4xl mb-3"></i>
                                <p class="text-sm">Tidak ada jenis sampah tersedia.</p>
                            </div>

                            <div class="space-y-4" v-else>

                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-xs text-gray-500">Step {{ step }} dari {{ totalSteps }}</span>
                                    <div class="flex gap-2">
                                        <button v-for="i in totalSteps" :key="i" type="button" @click="step = i"
                                            :class="step === i ? 'bg-emerald-600 text-white' : 'bg-gray-200'"
                                            class="w-8 h-8 rounded-full text-xs font-bold transition">
                                            {{ i }}
                                        </button>
                                    </div>
                                </div>

                                <div v-for="(chunk, index) in chunks" :key="index">
                                    <div v-show="step === index + 1" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <div v-for="item in chunk" :key="item.sampah_id"
                                            class="p-3 rounded-lg border dark:border-gray-700 border-gray-300 bg-white text-black dark:text-white dark:bg-gray-900 shadow-sm">
                                            <div class="text-sm font-medium truncate capitalize">{{ item.nama }}</div>
                                            <div class="text-xs text-gray-500 mb-2 capitalize">Satuan: {{ item.satuan }}
                                            </div>
                                            <input type="number" step="0.01" v-model="item.jumlah"
                                                class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 text-sm focus:ring-2 focus:ring-emerald-500"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                                    <button type="button" @click="step = Math.max(step - 1, 1)" :disabled="step === 1"
                                        class="text-gray-500 disabled:opacity-30">
                                        ← Kembali
                                    </button>

                                    <button v-if="step < totalSteps" type="button" @click="step++"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                                        Lanjut →
                                    </button>

                                    <button v-else type="submit" :disabled="form.processing"
                                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-bold">
                                        {{ form.processing ? 'Menyimpan...' : 'Simpan Setoran' }}
                                    </button>
                                </div>
                            </div>


                        </FormWrapper>
                    </div>
                </div>
            </Transition>

            <!-- TABLE -->
            <div class=" p-4 ">
                <div class="bg-white dark:bg-gray-800  rounded-2xl shadow-sm ">

                    <!-- FILTER BAR -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 mb-4">

                        <div class="flex flex-wrap items-center justify-between mb-4">
                            <div class="flex gap-2 mb-4">
                                <button @click="viewMode = 'saldo'"
                                    :class="viewMode === 'saldo' ? 'bg-emerald-600 text-white' : 'bg-gray-200'"
                                    class="px-4 py-2 rounded-lg text-sm font-bold">
                                    Cek Saldo
                                </button>

                                <button @click="viewMode = 'sampah'"
                                    :class="viewMode === 'sampah' ? 'bg-blue-600 text-white' : 'bg-gray-200'"
                                    class="px-4 py-2 rounded-lg text-sm font-bold">
                                    Cek Sampah
                                </button>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                <div v-if="viewMode === 'sampah'"
                                    class="flex p-1 bg-gray-100 dark:bg-gray-900 rounded-2xl w-fit border border-gray-100 dark:border-gray-700">
                                    <button v-for="cat in categories" :key="cat"
                                        @click="activeCategory = cat; selectedSampah = ''"
                                        :class="['px-6 py-2 text-sm font-bold rounded-xl transition-all',
                                            activeCategory === cat ? 'bg-white shadow-md text-emerald-600' : 'text-gray-500']">
                                        {{ cat }}
                                    </button>
                                </div>


                            </div>

                        </div>



                        <!-- Row 2: Filter Bulan + Sampah + Jadwal -->
                        <div class="flex flex-wrap items-center gap-3">

                            <div class="flex items-center gap-3">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tahun:</label>
                                <select v-model="selectedYear"
                                    class="rounded-xl border-gray-200 text-sm font-bold text-emerald-700 focus:ring-emerald-500">
                                    <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-400 uppercase">Bulan:</label>

                                <select v-model="selectedBulan"
                                    class="rounded-xl border-gray-200 text-sm font-bold text-gray-700">

                                    <!-- SALDO -->
                                    <template v-if="viewMode === 'saldo'">
                                        <option value="">Semua Bulan</option>
                                    </template>

                                    <!-- SAMPAH -->
                                    <template v-else>
                                        <option value="">Pilih Bulan</option>
                                    </template>

                                    <option v-for="m in months" :key="m.id" :value="m.id">
                                        {{ m.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- MODE SAMPAH -->
                            <div v-if="viewMode === 'sampah'" class="flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-400 uppercase">Jenis Sampah:</label>
                                <select v-model="selectedSampah"
                                    class="rounded-xl border-gray-200 text-sm font-bold text-gray-700">
                                    <option value="">Semua Jenis</option>
                                    <option v-for="s in filteredSampahByKategori" :key="s.id" :value="s.id">
                                        {{ s.nama_sampah }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal:</label>
                                <select v-model="selectedJadwalFilter"
                                    class="rounded-xl border-gray-200 text-sm font-bold text-gray-700 focus:ring-emerald-500">
                                    <option value="">Semua Jadwal</option>
                                    <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.id">
                                        {{ formatDate(j.tanggal_setoran) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Badge info mode aktif -->
                            <div class="ml-auto">
                                <span v-if="selectedBulan"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                    Mode: Kolom per Jenis Sampah
                                </span>
                                <span v-else
                                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                                    Mode: Kolom per Bulan
                                </span>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-900  overflow-hidden p-4">

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

                        <div v-if="isMobile" class="flex flex-col gap-2 mb-4">

                            <!-- Search -->
                            <input v-model="mobileSearch" type="text" placeholder="Cari nasabah..."
                                class="border rounded-lg px-3 py-2 text-sm w-full">

                            <!-- Show -->
                            <select v-model="mobilePerPage" class="border rounded-lg px-3 py-2 text-sm w-full">
                                <option :value="5">5</option>
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                            </select>

                        </div>
                        <div v-else class="flex flex-wrap gap-3">
                            <div class="flex items-end gap-2">
                                <label
                                    class="text-xs m-auto  font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                                <input @keyup="handleSearch" type="text"
                                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                                    placeholder="Ketik...">
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

                    <!-- MOBILE VIEW -->
                    <div v-if="isMobile" class="space-y-3">
                        <div v-for="row in paginatedMobileData" :key="row.id || row.fullName"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">

                            <!-- Nama -->
                            <div class="font-bold text-gray-800 dark:text-white">
                                {{ row.fullName }}
                            </div>

                            <!-- Dynamic Data -->
                            <div class="mt-2 space-y-1 text-sm">
                                <div v-for="(col, i) in dynamicColumns" :key="i"
                                    class="flex justify-between border-b py-1">

                                    <span class="text-gray-500">
                                        {{ col.title }}
                                    </span>

                                    <span class="font-semibold">
                                        {{ col.render(null, 'display', row) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="mt-3 flex justify-between font-bold text-emerald-600 border-t pt-2">
                                <span>Total</span>
                                <span>{{ row.totalTahunan.toLocaleString('id-ID') }}</span>
                            </div>

                            <div class="mt-3">
                                <button @click="goToDetail(row.id)"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-semibold">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>

                        <div v-if="isMobile" class="flex justify-between items-center mt-4">

                            <button @click="prevMobilePage" :disabled="mobilePage === 1"
                                class="px-3 py-1 bg-gray-200 rounded disabled:opacity-30">
                                ← Prev
                            </button>

                            <span class="text-sm font-semibold">
                                {{ mobilePage }} / {{ totalMobilePages }}
                            </span>

                            <button @click="nextMobilePage" :disabled="mobilePage === totalMobilePages"
                                class="px-3 py-1 bg-gray-200 rounded disabled:opacity-30">
                                Next →
                            </button>

                        </div>

                        <div class="text-xs text-gray-500">
                            Menampilkan {{ paginatedMobileData.length }} dari {{ filteredMobileData.length }} data
                        </div>
                    </div>
                    <DataTable v-else
                        :key="viewMode + selectedBulan + selectedSampah + selectedJadwalFilter + activeCategory"
                        :options="dtOptions" :data="processedData" ref="dtInstance">
                        <thead class="dark:text-white text-black">
                        </thead>
                        <tfoot class="bg-gray-50 dark:bg-gray-900 font-bold">
                            <tr class="text-gray-800 dark:text-white border-t-2 border-emerald-500">
                                <th class="text-left py-4 px-2 uppercase">Total Keseluruhan</th>
                                <th v-for="(col, i) in dynamicColumns" :key="i" class="text-center py-4">0</th>
                                <th class="text-right py-4 px-2 text-emerald-600">0</th>
                            </tr>
                        </tfoot>
                    </DataTable>
                </div>
            </div>

        </div>


    </AuthenticatedLayout>
</template>

<style scoped>
/* Paksa tabel menggunakan layout fixed agar tidak meluber */
:deep(.dataTable) {
    table-layout: fixed !important;
    width: 100% !important;
    font-size: 14px !important;
    /* Naikan sedikit dari 10px */
}

/* Kolom NASABAH lebih lebar */
:deep(.dataTable thead th:first-child),
:deep(.dataTable tbody td:first-child),
:deep(.dataTable tfoot th:first-child) {
    width: 12% !important;
}

:deep(.dataTable tbody td:first-child) {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Kolom bulan lebih proporsional */
:deep(.col-bulan) {
    width: 6.2% !important;
    padding: 6px 3px !important;
}

/* Kolom TOTAL */
:deep(.col-total) {
    width: 9% !important;
}

:deep(.dataTable tbody td) {
    padding: 10px 4px !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

:deep(.dataTables_wrapper) {
    width: 100%;
    overflow-x: hidden;
}
</style>
