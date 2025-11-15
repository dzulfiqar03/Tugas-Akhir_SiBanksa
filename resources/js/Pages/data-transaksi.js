$(document).ready(function () {

    let table = new DataTable('#dataTransaksi', {
        pageLength: 5,
        responsive: true,
        lengthMenu: [5, 10, 25, 50],
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
                        className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
                        title: 'Data Sampah',
                        customize: function (doc) {
                            // Atur margin halaman PDF
                            doc.pageMargins = [40, 60, 40, 40];

                            // Tambahkan logo + namaSampah di atas tabel
                            doc.content.splice(0, 0, {
                                columns: [
                                    {
                                        image: 'data:image/png;base64,REPLACE_WITH_BASE64_LOGO', // Ganti base64 logomu di sini
                                        width: 60
                                    },
                                    {
                                        text: 'Bank Sampah - Data Sampah',
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
                        exportOptions: {
                            columns: ':not(.no-print)'  // ← semua kolom kecuali yg punya class no-print
                        },
                        customize: function (win) {
                            // Tambahkan logo dan heading di atas
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
                        <p style="font-size: 14px; margin: 0;">Laporan Data Sampah</p>
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

                ]
            },
            topEnd: function () {
                return $(`
<div class="lg:flex justify-end hidden items-center gap-4">
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

            emptyTable: "Tidak ada data tersedia"
        }
    });

    let table2 = new DataTable('#dataRekening', {
        pageLength: 5,
        responsive: true,
        lengthMenu: [5, 10, 25, 50],
        autoWidth: false,
        info: false,
        columnDefs: [
            { width: '13px', targets: 0 },
            { width: '25px', targets: 1 },
            { width: '80px', targets: 2 },

        ],
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa-solid fa-file-pdf"></i>',
                        className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-md text-xs shadow-sm',
                        title: 'Data Rekening',
                        customize: function (doc) {
                            doc.pageMargins = [40, 60, 40, 40];
                            doc.content.splice(0, 0, {
                                columns: [
                                    {
                                        image: 'data:image/png;base64,REPLACE_WITH_BASE64_LOGO',
                                        width: 50
                                    },
                                    {
                                        text: 'Bank Sampah - Data Rekening',
                                        alignment: 'right',
                                        fontSize: 14,
                                        bold: true,
                                        margin: [0, 20, 0, 0]
                                    }
                                ],
                                columnGap: 10
                            });
                            doc.styles.tableHeader.fillColor = '#f1f1f1';
                            doc.styles.tableHeader.color = '#333333';
                            doc.defaultStyle.fontSize = 9;
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa-solid fa-file-excel"></i>',
                        className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-2 py-1 rounded-md text-xs shadow-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa-solid fa-print"></i>',
                        className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-2 py-1 rounded-md text-xs shadow-sm',
                        title: '',
                        exportOptions: {
                            columns: ':not(.no-print)'  // ← semua kolom kecuali yg punya class no-print
                        },
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
                        <p style="font-size: 14px; margin: 0;">Laporan Data Sampah</p>
                        <p style="font-size: 12px; margin: 0;">Dicetak pada: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <hr style="border: 1px solid #ccc; margin-bottom: 20px;">
            `);
                            $(win.document.body).find('table td')
                                .css({ 'padding': '4px', 'border': '1px solid #ddd' });
                        }
                    }
                ]
            },
            topEnd: function () {
                return $(`
                <div class="lg:flex justify-end hidden items-center gap-2">
                    <div class="flex items-center gap-2">
                        <label for="pageLength" class="text-xs text-gray-700 dark:text-gray-300">Tampilkan:</label>
                        <select id="pageLength"
                            class="appearance-none border dark:border-gray-600 rounded-md pl-2 pr-6 py-0.5 text-xs
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
            bottomEnd: 'paging'
        },
        language: {
            emptyTable: "Tidak ada data tersedia"

        }
    });

    // Sinkronkan dropdown page length manual
    $('#pageLength').on('change', function () {
        table2.page.len($(this).val()).draw();
    });





    const $card = $('#card_project');

    const $icon = $('.card-header').find('i');
    const $card_detail = $('#card_detail');

    $(document).on('click', '.btn-detail', function () {

        const isCollapsed = $card_detail.hasClass('collapsed-card-detail');

        if (isCollapsed) {
            // buka card
            $card_detail.removeClass('collapsed-card-detail');
            $card_detail.find('.card-body').slideDown(200);
            $icon.removeClass('fa-plus').addClass('fa-minus');
            $card_detail.find('.btn-cancel').removeClass('hidden');
            $card.find('.btn-tool').addClass('hidden');
            $card_detail.removeClass('hidden')


        } else {
            // tutup card
            $card_detail.addClass('collapsed-card-detail');
            $card_detail.find('.card-body').slideUp(200);
            $icon.removeClass('fa-minus').addClass('fa-plus');
            $card_detail.find('.btn-cancel').addClass('hidden');
            $card.find('.btn-tool').removeClass('hidden');

        }

        const data = $(this).data();


        // isi form
        $('#card_detail .card-body ul li span#nama_lengkap').text(data.id);
        $('#card_detail .card-body ul li span#rt').text(data.nama);
        $('#card_detail .card-body ul li span#nomor_rekening').text(data.total_transaksi);
        $('#card_detail .card-body ul li span#jenis_rekening').text(data.total_transaksi);
    })

    $(document).on('click', '.btn-tool', function () {

        // toggle collapsed state
        const isCollapsed = $card.hasClass('collapsed-card');


        if (isCollapsed) {
            // buka card
            $card.removeClass('collapsed-card');
            $card.find('.card-body').slideDown(200);
            $icon.removeClass('fa-plus').addClass('fa-minus');
            $card.find('.btn-cancel').removeClass('hidden');
            $card_detail.addClass('hidden')
        } else {
            // tutup card
            $card.addClass('collapsed-card');
            $card.find('.card-body').slideUp(200);
            $icon.removeClass('fa-minus').addClass('fa-plus');
            $card.find('.btn-cancel').addClass('hidden');
        }

        $('#form_book input[name="_method"]').val("POST");
        $('#form_book input[name="id"]').val("");
        $('#form_book input[name="nama_sampah"]').val("");
        $('#form_book input[name="satuan"]').val("");
        $('#form_book input[name="harga"]').val("");
        $('#form_book input[name="kategori"]').val("");
    });

    $(document).on('click', '.btn-cancel', function () {

        const isCollapsed = $card.hasClass('collapsed-card');

        // tutup card
        $card.addClass('collapsed-card');
        $card.find('.card-body').slideUp(200);
        $icon.removeClass('fa-minus').addClass('fa-plus');
        $card.find('.btn-cancel').addClass('hidden');
        $card.find('.btn-simpan').text('Simpan Buku')

    });
    $('#form_book').on('submit', function (e) {
        e.preventDefault();
        const method = $('#form_book input[name="_method"]').val();
        try {
            const post = () => {
                const formData = new FormData($('#form_book')[0]);

                const id = $('#form_book input[name="id"]').val();


                const url = method === 'POST' ?
                    `/book-add` :
                    `/book-update/${id}`;

                if (typeof ajax_post === 'function') {
                    $.ajax({
                        url: url,
                        type: formData,
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'Accept': 'application/json'
                        },
                        success: function (result) {
                            if (result.code === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: result.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Error', result.message ??
                                    'Gagal menghapus data', 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Terjadi kesalahan server', 'error');
                        },
                        complete: function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    });
                } else {

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            if (result.code == 200) location.reload();
                            else alert(result.message ?? 'Save failed');
                        }
                    });
                }
            };

            url = method === 'POST' ?
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to add this book?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, add it!'
                }).then((result) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => post());


                }) : Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this book?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => post());


                })
        } catch (error) {
            if (typeof show_error === 'function') show_error({
                html: error
            });
            else alert(error);
        }
    });

    // Edit button - delegated (works with DataTables)
    $(document).on('click', '#dataTransaksi .btn-edit', function () {
        const data = $(this).data();

        // isi form
        $('#form_book input[name=_method]').val("PUT");
        $('#form_book input[name=id]').val(data.id);
        $('#form_book input[name=nama_sampah]').val(data.nama_sampah);
        $('#form_book input[name=satuan]').val(data.satuan);
        $('#form_book input[name=harga]').val(data.harga);
        $('#form_book input[name=kategori]').val(data.kategori);

        const isCollapsed = $('#card_project').hasClass('collapsed-card');
        // open card
        $('#card_project').removeClass('collapsed-card');


        if (isCollapsed) {
            $card.removeClass('collapsed-card');
            $card.find('.card-body').slideDown(200);
            $card.find('.btn-cancel').removeClass('hidden');

            $card.find('.btn-simpan').text('Update Buku')
        } else {
            $card.addClass('collapsed-card');
            $card.find('.card-body').slideUp(200);
            $card.find('.btn-cancel').addClass('hidden');
        }
        $('#card_project .btn-cancel').removeClass('hidden');
    });

    // Cancel button
    $(document).on('click', '#card_project .btn-cancel', function () {
        $('#form_book input[name="id"]').val("");
        $('#form_book input[name="_method"]').val("POST");

        $('#card_project .btn-cancel').addClass('hidden');

        // close form
        $('#card_project').addClass('collapsed-card');
        $('#card_project .card-body').hide();
        $('#card_project .btn-tool>i').removeClass('fa-minus').addClass('fa-plus');
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        const token = $('meta[name="csrf-token"]').attr('content');

        if (!id) {
            alert('ID tidak ditemukan!');
            return;
        }

        const post = () => {
            $.ajax({
                url: `/book-destroy/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    csrf_test_name: token
                },
                headers: {
                    'Accept': 'application/json'
                },
                success: function (result) {
                    if (result.code === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', result.message ?? 'Gagal menghapus data',
                            'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message ||
                        'Terjadi kesalahan server', 'error');
                }
            });
        };

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this book?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) post();
        });
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


function filterTable(category) {
    const rows = document.querySelectorAll("#table-body tr");
    rows.forEach(row => {
        const rowCategory = row.getAttribute("data-kategori");
        if (category === "Semua" || rowCategory === category) {
            row.style.display = ""; // Tampilkan baris
        } else {
            row.style.display = "none"; // Sembunyikan baris
        }
    });

    // Update tombol aktif
    const buttons = document.querySelectorAll("button");
    buttons.forEach(button => button.classList.remove("btn-success"));
    buttons.forEach(button => button.classList.add("btn-outline-secondary"));

    const activeButton = [...buttons].find(btn => btn.textContent === category);
    if (activeButton) {
        activeButton.classList.remove("btn-outline-secondary");
        activeButton.classList.add("btn-success");
    }
}

const items = json($items);

// Prepare data for Pie Chart
const labels = items.map(item => item.item);
const data = items.map(item => item.item_sold);

const ctx = document.getElementById('pieChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            label: 'Item Sold',
            data: data,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function (tooltipItem) {
                        const value = tooltipItem.raw;
                        return `${tooltipItem.label}: ${value} items`;
                    }
                }
            }
        }
    }
});
