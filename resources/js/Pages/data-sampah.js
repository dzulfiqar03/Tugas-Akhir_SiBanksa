$(document).ready(function () {

    let table = new DataTable('#dataSampah', {
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
            <option value="Daur Ulang">Daur Ulang</option>
            <option value="Non Daur Ulang">Non Daur Ulang</option>
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

    const $card = $('#card_project');

    const $icon = $('.card-header').find('i');
    $(document).on('click', '.btn-tool', function () {

        // toggle collapsed state
        const isCollapsed = $card.hasClass('collapsed-card');

        if (isCollapsed) {
            // buka card
            $card.removeClass('collapsed-card');
            $card.find('.card-body').slideDown(200);
            $icon.removeClass('fa-plus').addClass('fa-minus');
            $card.find('.btn-cancel').removeClass('hidden');
        } else {
            // tutup card
            $card.addClass('collapsed-card');
            $card.find('.card-body').slideUp(200);
            $icon.removeClass('fa-minus').addClass('fa-plus');
            $card.find('.btn-cancel').addClass('hidden');
        }

        $('#formSampah input[name="_method"]').val("POST");
        $('#formSampah input[name="id"]').val("");
        $('#formSampah input[name="nama_sampah"]').val("");
        $('#formSampah input[name="satuan"]').val("");
        $('#formSampah input[name="harga"]').val("");
        $('#formSampah input[name="kategori"]').val("");
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



    //     $('#formSampah').on('submit', function(e) {
    //     e.preventDefault();

    //     // reset error
    //     $('#formSampah input, #formSampah select').removeClass('border-red-500 ring-1 ring-red-500');
    //     $('#error-message').fadeOut();

    //     const method = $('#formSampah input[name="_method"]').val();
    //     const url = $(this).attr('action');
    //     const formData = new FormData(this);

    //     $.ajax({
    //         url: url,
    //         method: 'POST',
    //         data: formData,
    //         contentType: false,
    //         processData: false,
    //         headers: { 
    //             'Accept': 'application/json',
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function(res) {
    //             Swal.fire({
    //                 icon: 'success',
    //                 title: method === 'POST' ? 'Added!' : 'Updated!',
    //                 text: res.message || 'Success',
    //                 timer: 1500,
    //                 showConfirmButton: false
    //             }).then(() => location.reload());
    //         },
    //         error: function(xhr) {
    //             if (xhr.status === 422) {
    //                 const errors = xhr.responseJSON.errors;
    //                 let errorHtml = '';
    //                 let count = 0;

    //                 Object.keys(errors).forEach(key => {
    //                     errors[key].forEach(msg => {
    //                         errorHtml += `
    //                         <li class="text-[11px] text-red-600 flex items-center gap-2">
    //                             <span class="w-1 h-1 bg-red-400 rounded-full"></span>
    //                             ${msg}
    //                         </li>`;
    //                         count++;
    //                     });

    //                     // Highlight input
    //                     $(`[name="${key}"]`).addClass('border-red-500 ring-1 ring-red-500');
    //                 });

    //                 $('#error-list').html(errorHtml);
    //                 $('#error-count').text(count);
    //                 $('#error-message').removeClass('hidden').fadeIn();

    //                 // SweetAlert untuk gagal
    //                 Swal.fire('Gagal!', 'Silakan periksa kembali inputan Anda.', 'error');
    //             } else {
    //                 Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
    //             }
    //         }
    //     });
    // });

    $('#formSampah').on('submit', function (e) {
        e.preventDefault();

        const method = $('#formSampah input[name="_method"]').val();
        const id = $('#formSampah input[name="id"]').val();


        const url = method === 'POST' ?
            `/Bank Sampah/Sampah/Create` :
            `/Bank Sampah/Sampah/Update/${id}`;



        try {
            const post = () => {
                const formData = new FormData(this);


                $.ajax({
                    url: url,
                    method: 'POST', // tetap POST
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '';
                            Object.keys(errors).forEach(key => {
                                errors[key].forEach(msg => {
                                    errorHtml += `<li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           ${msg}
                       </li>`;
                                });
                                $(`[name="${key}"]`).addClass('border-red-500 ring-1 ring-red-500');
                            });
                            $('#error-list').html(errorHtml);
                            $('#error-message').removeClass('hidden').fadeIn();
                            Swal.fire('Gagal!', 'Silakan periksa kembali inputan Anda.', 'error');
                        } else {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
                        }
                    },
                    complete: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                });
            };


            method === 'POST' ?
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to add this Trash?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, add it!'
                }).then(() => post()) : Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this Trash?",
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
    $(document).on('click', '#dataSampah .btn-edit', function () {
        const data = $(this).data();

        // isi form
        $('#formSampah input[name=_method]').val("PUT");
        $('#formSampah input[name=id]').val(data.id);
        $('#formSampah input[name=nama_sampah]').val(data.nama_sampah);
        $('#formSampah input[name=satuan]').val(data.satuan);
        $('#formSampah input[name=harga]').val(data.harga);

        $('#formSampah select[name="kategori"]').val(data.kategori).trigger('change');

        const isCollapsed = $('#card_project').hasClass('collapsed-card');
        // open card
        $('#card_project').removeClass('collapsed-card');


        if (isCollapsed) {
            $card.removeClass('collapsed-card');
            $card.find('.card-body').slideDown(200);
            $card.find('.btn-cancel').removeClass('hidden');

            $card.find('.btn-simpan').text('Update Sampah')
        } else {
            $card.addClass('collapsed-card');
            $card.find('.card-body').slideUp(200);
            $card.find('.btn-cancel').addClass('hidden');
        }
        $('#card_project .btn-cancel').removeClass('hidden');
    });

    // Cancel button
    $(document).on('click', '#card_project .btn-cancel', function () {
        $('#formSampah input[name="id"]').val("");
        $('#formSampah input[name="_method"]').val("POST");

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
                url: `/Bank Sampah/Sampah/Delete/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: token
                },
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
