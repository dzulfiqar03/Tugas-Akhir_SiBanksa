$(document).ready(function () {

    let table = new DataTable('#tracking', {
        pageLength: 5,
        responsive: true,
        lengthMenu: [5, 10, 25, 50],
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa-solid fa-file-csv mr-2"></i> CSV',
                        className: 'export-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
                        className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa-solid fa-print mr-2"></i> Print',
                        className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
                    }
                ]
            },
            topEnd: function () {
                return $(`
<div class="flex justify-end items-center gap-4">
    <!-- Pencarian -->
    <div class="flex items-center gap-2">
        <label for="tableSearch" class="text-sm text-gray-700 dark:text-gray-300">Cari:</label>
        <input id="tableSearch" type="text" 
            class="border dark:border-gray-600 rounded-md px-2 py-1 text-sm 
                   bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
            placeholder="Ketik untuk mencari...">
    </div>

    <!-- Filter kategori -->
    <div class="flex items-center gap-2 relative">
        <label for="kategoriFilter" class="text-sm text-gray-700 dark:text-gray-300">Kategori:</label>
        <select id="kategoriFilter"
            class="appearance-none border dark:border-gray-600 rounded-md pl-2 pr-6 py-1 text-sm
                   bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100">
            <option value="">Semua</option>
            <option value="Kering">Kering</option>
            <option value="Plastik">Plastik</option>
            <option value="Logam">Logam</option>
            <option value="Cair">Cair</option>
        </select>
       
    </div>

    <!-- Page Length -->
    <div class="flex items-center gap-2 relative">
        <label for="pageLength" class="text-sm text-gray-700 dark:text-gray-300">Tampilkan:</label>
        <select id="pageLength"
            class="appearance-none border dark:border-gray-600 rounded-md pl-2 pr-6 py-1 text-sm
                   bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        
    </div>
</div>


                `)[0];
            },
            bottomStart: 'info',
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
    });


    // 🔍 Search custom
    $('#tableSearch').on('keyup', function () {
        table.search(this.value).draw();
    }); $('#pageLength').on('change', function () {
        table.page.len(parseInt(this.value)).draw();
    });
    // Filter kategori
    $('#kategoriFilter').on('change', function () {
        table.column(4).search(this.value).draw(); // kolom kategori index 4
    });

    // === Conditional formatting: highlight baris dengan harga tertinggi ===
    function highlightMaxRow() {
        let max = 0, maxIndex = -1;
        table.rows({ search: 'applied' }).every(function (rowIdx, tableLoop, rowLoop) {
            const harga = parseFloat(this.data()[3]);
            if (harga > max) {
                max = harga;
                maxIndex = rowIdx;
            }
        });
        // Hapus highlight sebelumnya
        $('#userTable tbody tr').removeClass('highlight-max');
        if (maxIndex >= 0) {
            $(table.row(maxIndex).node()).addClass('highlight-max');
        }
    }

    // Jalankan pertama kali & setiap update
    highlightMaxRow();
    table.on('draw', highlightMaxRow);
});
