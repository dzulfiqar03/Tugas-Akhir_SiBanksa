<script setup>
import { ref, computed } from 'vue';
import { useForm, router, Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import FormWrapper from '@/Components/FormWrapper.vue';

import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';
import InputLabel from '@/Components/InputLabel.vue';
import 'datatables.net-dt/css/dataTables.dataTables.css';

import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net'
import Buttons from 'datatables.net-buttons'
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5'
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print'
import Responsive from 'datatables.net-responsive-dt'

import 'datatables.net-dt/css/dataTables.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'

DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)
window.JSZip = jszip;
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;

const typeForm = ref('Document');
const props = defineProps({
    formdata: Object,
    sidebardata: Object,
    IDUser:Number,
    document:Array,
    image:Array,
        jadwalPelaksanaan: Array,
        IDRT:Number,
        IDRW:Number
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
    selectedImageUrl.value = typeForm.value === 'Document'?
    `/storage/files/documentUser/BankSampah/RT0${props.IDRT}/${fileName}`:
        `/storage/photo/evidenceUser/BankSampah/RT0${props.IDRT}/${fileName}`;
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
            }):router.delete(route('delete-evidence', id), {
                onSuccess: () => Swal.fire('Dihapus!', 'Evidence berhasil dihapus.', 'success')
            });
        }
    });
};

window.deleteDoc = deleteData; 
const formatChildRow = (d) => {
    
    let photoHtml = typeForm.value === 'Document'? d.document.map(p => `
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
    `).join(''):d.photos.map(p => `
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

    return typeForm.value === 'Document'? `
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500">
            <p class="text-xs font-bold text-emerald-600 mb-3 uppercase tracking-wider">Dokumen (${d.document.length} File):</p>
            <div class="flex flex-wrap gap-4">
                ${photoHtml || '<p class="text-gray-400 italic">Tidak ada Dokumen.</p>'}
            </div>
        </div>
    `:`
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
        if(icon) icon.style.transform = 'rotate(0deg)';
    } else {
        row.child(formatChildRow(row.data())).show();
        tr.classList.add('shown');
        if(icon) icon.style.transform = 'rotate(90deg)';
    }
};
const dtOptions = computed(() => ({
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    columns: typeForm.value === 'Document' ?  [
        { 
            data: null, 
            orderable: false, 
            className: 'no-print details-control text-center' 
        } ,
        { data: 'name', className:'text-black dark:text-white',render: (data) => `<strong>Dokumen: ${data}</strong>` },
    { data: 'tanggal_setoran',className:'text-black dark:text-white', render: (data) => `<strong>Jadwal: ${data}</strong>` },
    { 
        data: 'document', 
        render: (data) => `<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">${data.length} Dokumen</span>` 
    },
        
    ]: [
     { 
            data: null, 
            orderable: false, 
            className: 'no-print text-black dark:text-white details-control text-center text-black dark:text-white' 
        } ,
    { data: 'name', className:'text-black dark:text-white',render: (data) => `<strong>Jadwal: ${data}</strong>` },
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
                        className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
                        title: typeForm.value === 'Document'? 'Data Dokumen Bank Sampah RT0' + " " +  props.IDRT:'Data Evidence Bank Sampah RT0' + " " +  props.IDRT,
                        exportOptions: {
                            columns: ':not(.no-print)'  // ← semua kolom kecuali yg punya class no-print
                        },
                        customize: function (doc) {
                            // Atur margin halaman PDF
                            doc.pageMargins = [40, 60, 40, 40];

                            // Tambahkan logo + namaSampah di atas tabel
                            doc.content.splice(0, 0, {
                                columns: [
                                   {
                                        text: 'SI BANKSA',
                                        alignment: 'left',
                                        fontSize: 16,
                                        bold: true,
                                        margin: [0, 20, 0, 0]
                                    },
                                    {
                                        text: typeForm.value === 'Document'? 'Bank Sampah - Data Dokumen Bank Sampah RT0' + " " +  props.IDRT:'Bank Sampah - Data Evidence Bank Sampah RT0' + " " +  props.IDRT,
                                        alignment: 'right',
                                        fontSize: 16,
                                        bold: true,
                                        margin: [0, 20, 0, 0]
                                    }
                                ],
                                columnGap: 10
                            });

                            // Tambahkan garis pemisah
                            doc.content.splice(1, 0, {
                                canvas: [
                                    {
                                        type: 'line',
                                        x1: 0,
                                        y1: 0,
                                        x2: 515,
                                        y2: 0,
                                        lineWidth: 1,
                                        lineColor: '#cccccc'
                                    }
                                ],
                                margin: [0, 10, 0, 10]
                            });

                            // Atur gaya tabel (opsional)
                            doc.styles.tableHeader.fillColor = '#f1f1f1';
                            doc.styles.tableHeader.color = '#333333';
                            doc.defaultStyle.fontSize = 10;
                        }
                    },

                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
                        className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa-solid fa-print mr-2"></i> Print',
                        className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
                        title: '', // kosongin biar gak dobel namaSampah default
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-family', 'Poppins, sans-serif')
                                .prepend(`
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                                <h1 class="py-5 text-2xl font-semibold text-gray-800 dark:text-gray-100 transition-all duration-300 font-[Poppins] text-center w-full"
            >
            <span class="font-light">Si</span>
            Banksa
        </h1>
                        
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 14px; margin: 0;">${typeForm === 'Document'? 'Laporan Data Dokumen Bank Sampah RT0' + " " + props.IDRT:'Laporan Data Evidence Bank Sampah RT0' + "" + props.IDRT}</p>
                        <p style="font-size: 12px; margin: 0;">Dicetak pada: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <hr style="border: 1px solid #ccc; margin-bottom: 20px;">
            `);

                            // Styling tambahan (opsional)
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css({
                                    'font-size': '12px',
                                    'width': '100%',
                                    'border-collapse': 'collapse'
                                });

                            $(win.document.body).find('table th')
                                .css({
                                    'background-color': '#f1f1f1',
                                    'color': '#333',
                                    'padding': '6px',
                                    'border': '1px solid #ddd'
                                });

                            $(win.document.body).find('table td')
                                .css({
                                    'padding': '6px',
                                    'border': '1px solid #ddd'
                                });
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
        .draw(): dtInstance.value.dt
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


const handleSubmit = () => {
    
const url = isEdit.value 
    ? (typeForm.value === 'Document' ?  route('update-document', form.id)  :  route('update-evidence', form.id))
    : (typeForm.value === 'Document' ? route('add-document') : route('add-evidence'));
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire('Berhasil!', 'Data kepengurusan telah diproses.', 'success');
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



const form = useForm({

    name: typeForm.value === 'Document'? '': [],
    id_userdetail: props.IDUser,
    id_jadwal: '',
    fileDoc: [],
    imgEvidence:[]
});

const changeTab = (tab) => {
    typeForm.value = tab;
    step.value = 1;
    form.clearErrors();
};

const renamedFileList = computed(() => { 
    return typeForm.value === 'Document'? form.fileDoc.map((file, index) => {
        const extension = file.name.split('.').pop();
        return {
            original: file.name,
            dynamic: `Dokumen${form.name || 'Dokumen'}_BankSampahRT0${props.IDRT}_${index + 1}.${extension}`,
            size: file.size,
            
        };
    }):form.imgEvidence.map((file, index) => {
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
    typeForm.value === 'Document'?
    props.document.forEach(item => {
        const key = item.id_jadwal; // Tetap gunakan ID sebagai kunci agar unik
        
        if (!groups[key]) {
            const jadwal = props.jadwalPelaksanaan.find(j => j.id === key);
            
            groups[key] = {
                id_jadwal: key,
                name:item.name,
                tanggal_setoran: jadwal ? jadwal.tanggal_setoran : 'Tanggal Tidak Ditemukan',
                document: []
            };
        }
        groups[key].document.push(item);
    }):props.image.forEach(item => {
        if (!groups[item.name]) {
                        const jadwal = props.jadwalPelaksanaan.find(j => j.tanggal_setoran === item.name);

            groups[item.name] = {
                name: item.name,
                id_userdetail: item.id_userdetail,
                                tanggal_setoran: jadwal ? jadwal.tanggal_setoran : 'Tanggal Tidak Ditemukan',

                photos: []
            };
        }
        groups[item.name].photos.push(item);
    });
    return Object.values(groups);
});

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
    { label: 'Data Pelaporan Ketua RW', url: route('data-pelaporanRW')  },
];
</script>

<template>
    <Head :title="'Data Pelaporan Upload Dokumen'" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div  class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Pelaporan Pelaksanaan Bank Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola dokumen dan bukti pelaksanaan bank sampah Anda.</p>
                </div>
                <div class="flex space-x-3">
                      <button v-if="props.document.length > 0 && props.image.length > 0 "
                                            @click="sendReminder(props.IDRW)"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Ajukan Persetujuan Ketua RW
                                        </button>

                                          <div v-else x-cloak
        class="px-4 py-1.5 rounded-full bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-bold uppercase tracking-wider">
       <h1 v-if="props.document.length = 0">Anda belum mengupload bukti hasi setoran</h1>
       <h1 v-else-if="props.image.length = 0">Anda belum mengupload bukti evidence</h1>
              <h1 v-else>Anda belum mengupload bukti</h1>

    </div>
                        
                                        <button @click="openCreateForm" 
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                    <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                    {{ showForm ? 'Batal' : 'Tambah Data' }}
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
                <div v-if="showForm" class="bg-white  accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">{{ isEdit ? 'Perbarui Data' : 'Input Data Baru' }}</h3>
    
                        
    <div class="flex flex-col w-full ">
       

          <FormWrapper v-if="typeForm === 'Document'"
            formName="formDocument" 
            :errors="form.errors" 
            :processing="form.processing"
            @submit="handleSubmit"
        >
    
            <input type="hidden" name="id_userdetail" v-model="form.id_userdetail">
            
            <div class="grid grid-cols-1 gap-4" :class="(form.type ==='file' ? 'md:grid-cols-1':'md:grid-cols-2')">

            <div class="flex flex-col">
        <label class="block mb-1 text-[11px] font-bold text-gray-400 uppercase dark:text-gray-300">Jadwal Pelaksanaan</label>
        <select @change="handleScheduleChange" v-model="form.id_jadwal" class="text-black w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
            <option value="" disabled>Pilih Jadwal</option>
            <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.id">
                {{ j.tanggal_setoran }}
            </option>
        </select>
        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
    </div>
    <template v-for="field in formdata.Dokumen" :key="field.name">
        
        <div v-if="field.name === 'name'" class="flex flex-col">
            <InputLabel :for="field.name" :value="field.title" />
            <select v-model="form.name" class="w-full h-11 rounded-xl dark:text-white text-black bg-gray-50 dark:bg-gray-800 border-gray-200 focus:ring-emerald-500 transition-all shadow-sm text-sm pl-5">
                <option value="">Pilih Jenis Upload</option>
                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
            </select>
            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
        </div>



    </template>

    <template v-for="field in formdata.Dokumen" :key="field.name">



        <div v-if="field.type === 'file' && field.name === 'fileDoc'" class="flex flex-col"> 
            <InputLabel :for="field.name" :value="field.title" />                        
            <input 
                :type="field.type" 
                :id="field.name"
                multiple
            @input="(e) => {
    const newFiles = Array.from(e.target.files);
    form.fileDoc = [...form.fileDoc, ...newFiles];
}"
                :placeholder="field.placeholder"
                class="w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200"
                :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
            >
            <p v-if="form[field.name]?.length" class="text-xs text-emerald-600 mt-2 font-medium">
            {{ form[field.name].length }} file terpilih
        </p>

        <ul v-if="form.fileDoc.length > 0" class="mt-2 space-y-1">
    <li v-for="(file, index) in renamedFileList" :key="index" class="text-xs text-gray-500 flex items-center">
        <svg class="w-3 h-3 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
        </svg>
        {{ file.dynamic }} ({{ (file.size / 1024).toFixed(1) }} KB)
    </li>
</ul>
            <div v-if="form.errors[field.name]" class="text-red-500 text-xs mt-1">{{ form.errors[field.name] }}</div>
        </div>

    </template>
</div>

 <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                            <button type="submit" class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50" :disabled="form.processing">
                                <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Dokumen' : 'Simpan Dokumen' }}
                            </button>
                        </div>

             </FormWrapper>

               <FormWrapper v-else
            formName="formEvidence" 
            :errors="form.errors" 
            :processing="form.processing"
            @submit="handleSubmit"
        >
    
            <input type="hidden" name="id_userdetail" v-model="form.id_userdetail">
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="flex flex-col">
        <label class="block mb-1 text-[11px] font-bold text-gray-400 uppercase dark:text-gray-300">Jadwal Pelaksanaan</label>
        <select @change="handleScheduleChange" v-model="form.name" class=" text-black  w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
            <option value="" disabled>Pilih Jadwal</option>
            <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.tanggal_setoran">
                {{ j.tanggal_setoran }}
            </option>
        </select>
        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
    </div>


    <template v-for="field in formdata.Dokumen" :key="field.name">
        <div v-if="field.type === 'file' && field.name === 'imgEvidence'" class="flex flex-col "> 
            <InputLabel :for="field.name" :value="field.title" />                        
            <input 
                :type="field.type" 
                :id="field.name"
                multiple
            @input="(e) => {
    const newFiles = Array.from(e.target.files);
    form.imgEvidence = [...form.imgEvidence, ...newFiles];
}"
                :placeholder="field.placeholder"
                class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 text-black e dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200"
                :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
            >
            <p v-if="form[field.name]?.length" class="text-xs text-emerald-600 mt-2 font-medium">
            {{ form[field.name].length }} file terpilih
        </p>

        <ul v-if="form.imgEvidence.length > 0" class="mt-2 space-y-1">
    <li v-for="(file, index) in renamedFileList" :key="index" class="text-xs text-gray-500 flex items-center">
        <svg class="w-3 h-3 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
        </svg>
        {{ file.dynamic }} ({{ (file.size / 1024).toFixed(1) }} KB)
    </li>
</ul>
            <div v-if="form.errors[field.name]" class="text-red-500 text-xs mt-1">{{ form.errors[field.name] }}</div>
        </div>
    </template>
</div>

 <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                            <button type="submit" class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50" :disabled="form.processing">
                                <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Evidence' : 'Simpan Evidence' }}
                            </button>
                        </div>
             </FormWrapper>
    </div>
                </div>
            </Transition>


            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                 <div  v-if="typeForm === 'Document'" class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

              <div class="flex flex-wrap mb-5 lg:mb-0 items-center gap-2">
            <button @click="exportData(0)" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button @click="exportData(1)" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button @click="exportData(2)" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
               <div class="flex flex-wrap md:flex-nowrap items-end justify-start gap-3">
                 <div class="flex items-end gap-2">
                <label class="text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                <input @keyup="handleSearch" type="text" 
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                    placeholder="Ketik...">
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                <select @change="handleCategoryFilter"
                    class="border border-gray-200 dark:border-gray-600 text-black  rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <option value="Hasil Setoran">Hasil Setoran</option>
                    <option value="Dokumen Lainnya">Dokumen Lainnya</option>
                </select>
            </div>

            <div class="flex items-center gap-2  pl-3">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                <select @change="handleLengthChange"
                    class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                    <option value="5" selected>5</option>
                    <option value="10" >10</option>
                    <option value="25">25</option>
                </select>
            </div>
            </div>
           
        </div>

                      <div  v-else class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

              <div class="flex flex-wrap mb-5 lg:mb-0 items-center gap-2">
            <button @click="exportData(0)" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button @click="exportData(1)" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button @click="exportData(2)" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
               <div class="flex flex-wrap md:flex-nowrap items-end justify-start gap-3">
                 <div class="flex items-end gap-2">
                <label class="text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                <input @keyup="handleSearch" type="text" 
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                    placeholder="Ketik...">
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                <select @change="handleCategoryFilter"
                    class="border border-gray-200 text-black  dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    <option value="">Semua</option>
                     <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.tanggal_setoran">
                {{ j.tanggal_setoran }}
            </option>
                </select>
            </div>

            <div class="flex items-center gap-2  pl-3">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                <select @change="handleLengthChange"
                    class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                    <option value="5" selected>5</option>
                    <option value="10" >10</option>
                    <option value="25">25</option>
                </select>
            </div>
            </div>
           
        </div>

            <div class=" bg-white dark:bg-gray-800 rounded-xl shadow">
                    <DataTable v-if="typeForm === 'Document'"
                    :data="groupedEvidence"
                        ref="dtInstance"
                        :options="dtOptions" 
                    class="w-full display stripe hover cell-border dark:text-white">
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
                                      

                                        <button  @click="onRowClick" 
     class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit">
                                 <i  
                 class="fas fa-plus-circle text-emerald-500 cursor-pointer"></i>
                            </button>
    
                                    </div>
                    </template>
 
     
                   </DataTable>

                     <DataTable v-else
                    :data="groupedEvidence"
                        ref="dtInstance"
                        :options="dtOptions" 
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
                                      

                                        <button  @click="onRowClick" 
     class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit">
                                 <i  
                 class="fas fa-plus-circle text-emerald-500 cursor-pointer"></i>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

       <template v-if="typeForm === 'Document'">
               <div class="w-full h-[80vh] md:h-[85vh]"> 
        <embed 
            :src="selectedImageUrl" 
            type="application/pdf" 
            class="w-full h-full rounded-lg shadow-inner"
        />
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
.dark td{
    color:white;
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

.accordion-wrapper > * {
    transition: opacity 0.2s;
}


.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #10b981 !important;
    border: none !important;
    color: white !important;
    border-radius: 8px;
}
.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
    font-size: 0.8rem;
    color: #ffffff !important;
    margin-top: 1rem;
}
.dark .dataTables_wrapper .dataTables_length, 
.dark .dataTables_wrapper .dataTables_filter, 
.dark .datatable .dt-info, 
.dark .dataTables_wrapper .dataTables_processing, 
.dark .datatable  .dt-paging {
    color: #ffffff !important;
}
.dataTables_filter { display: none; } /* Kita pakai custom search di atas */

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateY(-10px); opacity: 0; }

.progress-flow {
  width: 100%;
  background: linear-gradient(
    110deg,
    #3b82f6 25%,
    #60a5fa 37%,
    #3b82f6 63%
  );
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