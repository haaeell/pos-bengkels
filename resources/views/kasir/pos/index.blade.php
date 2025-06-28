@extends('layouts.dashboard')

@section('content')
    <style>
        .card-product {
            border: 1px solid #ccc;
            border-radius: 12px;
        }

        .card-product:hover {
            border: 1px solid blue;
        }

        .card-product.active {
            border: 1px solid rgb(34, 115, 236);
        }
    </style>

    <div class="row">
        <div class="col-lg-6 col-md-7 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <input type="text" class="form-control mb-3" id="search-product" placeholder="Search for products...">

                    <div class="row d-flex p-3" id="product-list">
                        @foreach ($products as $product)
                            <div class="col-lg-3 col-md-4 col-sm-6 card-product m-2"
                                data-category="{{ $product->description }}" data-id="{{ $product->id }}"
                                style="cursor: pointer;" data-price="{{ $product->price }}"
                                data-qty="{{ $product->quantity }}">
                                <div class="my-3 text-center">
                                    <img src="{{ asset($product->image) }}"
                                        style="width: 50px;height: 50px; object-fit: cover; border-radius: 50%;"
                                        alt="{{ $product->name }}">
                                    <h7 class="mt-3 mb-0">Produk {{ $product->name }}</h7>
                                    <p class="mb-0 text-danger mt-3">
                                        {{ formatRupiah($product->price) }}
                                    </p>
                                    <span>
                                        Sisa : {{ $product->quantity }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-5 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer-name" class="form-label">Nama Pelanggan:</label>
                        <input type="text" class="form-control" id="customer-name" placeholder="Masukkan nama pelanggan">
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Total Pembayaran:</label>
                        <span id="total-payment" class="text-danger fw-semibold">Rp 0</span>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Jenis Pembayaran</label>
                        <div class="d-flex flex-column">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment-method" id="payment-cash"
                                    value="cash">
                                <label class="form-check-label" for="payment-cash">
                                    <iconify-icon icon="material-symbols:attach-money-rounded"
                                        class="fs-5 me-2"></iconify-icon>
                                    Tunai
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment-method"
                                    id="payment-bank-transfer" value="bank_transfer">
                                <label class="form-check-label" for="payment-bank-transfer">
                                    <iconify-icon icon="bi:bank" class="fs-5 me-2"></iconify-icon>
                                    Transfer Bank
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount-paid" class="form-label">Jumlah Dibayar</label>
                        <input type="text" class="form-control" id="amount-paid" placeholder="Masukkan jumlah">
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Kembalian:</strong>
                        <span id="change">Rp 0</span>
                    </div>
                    <button class="btn btn-primary w-100 mt-3" id="pay-button">Bayar Sekarang</button>
                    <button class="btn btn-danger w-100 mt-2" id="cancel-button">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const productsPerPage = 9;
            const $products = $('#product-list .card-product');
            const totalProducts = $products.length;
            const totalPages = Math.ceil(totalProducts / productsPerPage);
            const $pagination = $('#pagination');

            function setupPagination(currentPage = 1) {
                $pagination.empty();

                const visiblePages = 5; // jumlah maksimal nomor halaman yang ditampilkan
                let startPage = Math.max(1, currentPage - Math.floor(visiblePages / 2));
                let endPage = startPage + visiblePages - 1;

                if (endPage > totalPages) {
                    endPage = totalPages;
                    startPage = Math.max(1, endPage - visiblePages + 1);
                }

                // Prev button
                $pagination.append(`
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a href="#" class="page-link" id="prev-page">&laquo;</a>
        </li>
    `);

                // Page numbers
                for (let i = startPage; i <= endPage; i++) {
                    $pagination.append(`
            <li class="page-item ${i === currentPage ? 'active' : ''}" data-page="${i}">
                <a href="#" class="page-link">${i}</a>
            </li>
        `);
                }

                // Next button
                $pagination.append(`
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a href="#" class="page-link" id="next-page">&raquo;</a>
        </li>
    `);
            }

            let currentPage = 1;

            function showPage(page) {
                if (page < 1) page = 1;
                if (page > totalPages) page = totalPages;

                $products.hide();
                const start = (page - 1) * productsPerPage;
                const end = start + productsPerPage;

                $products.slice(start, end).show();

                setupPagination(page); // update pagination dengan halaman saat ini

                currentPage = page;
            }

            // Panggil awal
            showPage(1);

            // Event pagination click
            $pagination.on('click', 'li.page-item', function(e) {
                e.preventDefault();
                if ($(this).hasClass('disabled') || $(this).hasClass('active')) return;

                if ($(this).find('#prev-page').length) {
                    showPage(currentPage - 1);
                } else if ($(this).find('#next-page').length) {
                    showPage(currentPage + 1);
                } else {
                    const page = parseInt($(this).data('page'));
                    if (!isNaN(page)) {
                        showPage(page);
                    }
                }
            });


            $('.card-product').on('click', function() {
                const productPrice = parseFloat($(this).data('price'));
                const productName = $(this).find('h7').text();
                const productId = $(this).data('id');
                const existingRow = $('#cart-items').find(`tr:contains(${productName})`);
                const stock = parseInt($(this).data('qty'));

                if (existingRow.length) {
                    const quantity = existingRow.find('span');
                    const currentQuantity = parseInt(quantity.text());

                    if (currentQuantity + 1 > stock) {
                        Swal.fire({
                            title: "Stok Tidak Cukup!",
                            text: `Stok produk "${productName}" hanya tersisa ${stock}.`,
                            icon: "warning",
                        });
                        return;
                    }
                    quantity.text(parseInt(quantity.text()) + 1);
                    updateRowTotal(existingRow, productPrice);
                } else {
                    if (stock < 1) {
                        Swal.fire({
                            title: "Stok Habis!",
                            text: `Produk "${productName}" sudah habis.`,
                            icon: "error",
                        });
                        return;
                    }
                    $('#cart-items').append(`
                <tr data-id="${productId}">
                    <td>${productName}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm decrease">-</button>
                            <span class="mx-2">1</span>
                            <button class="btn btn-outline-secondary btn-sm increase">+</button>
                        </div>
                    </td>
                    <td>${formatRupiah(productPrice)}</td>
                    <td>${formatRupiah(productPrice)}</td>
                    <td><button class="btn btn-danger btn-sm remove">Hapus</button></td>
                </tr>
            `);
                }
                updateTotal();
                $('.card-product').removeClass('active');
                $(this).addClass('active');
            });

            $('#cart-items').on('click', '.increase', function() {
                const row = $(this).closest('tr');
                const quantity = row.find('span');
                const currentQuantity = parseInt(quantity.text());
                const productId = row.data('id');
                const stock = parseInt($(`.card-product[data-id="${productId}"]`).data('qty'));
                const unitPrice = parseFloat(row.find('td:nth-child(3)').text().replace(/[^\d]/g, ''));

                if (currentQuantity + 1 > stock) {
                    Swal.fire({
                        title: "Stok Tidak Cukup!",
                        text: `Stok hanya tersisa ${stock}.`,
                        icon: "warning",
                    });
                    return;
                }

                quantity.text(currentQuantity + 1);
                const newTotal = (currentQuantity + 1) * unitPrice;
                row.find('td:nth-child(4)').text(formatRupiah(newTotal));
                updateTotal();
            });

            $('#cart-items').on('click', '.decrease', function() {
                const row = $(this).closest('tr');
                const quantity = $(this).siblings('span');
                const newQuantity = parseInt(quantity.text()) - 1;
                if (newQuantity >= 1) {
                    quantity.text(newQuantity);
                    const price = parseFloat(row.find('td:nth-child(3)').text().replace(/[^\d]/g, ''));
                    updateRowTotal(row, price);
                    updateTotal();
                }
            });

            $('#cart-items').on('click', '.remove', function() {
                $(this).closest('tr').remove();
                updateTotal();
            });

            function updateRowTotal(row, unitPrice) {
                const quantity = parseInt(row.find('span').text());
                const totalPrice = unitPrice * quantity;
                row.find('td:nth-child(4)').text(formatRupiah(totalPrice));
            }

            function updateTotal() {
                let total = 0;
                $('#cart-items tr').each(function() {
                    const totalPrice = parseFloat($(this).find('td:nth-child(4)').text().replace(/[^\d]/g,
                        ''));
                    total += totalPrice;
                });
                $('#total-payment').text(formatRupiah(total));
            }

            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('#search-product').on('input', function() {
                const query = $(this).val().toLowerCase();
                $products.each(function() {
                    const productName = $(this).find('h7').text().toLowerCase();
                    $(this).toggle(productName.includes(query));
                });

                // Update pagination after filtering
                const $visibleProducts = $products.filter(':visible');
                const filteredCount = $visibleProducts.length;
                const filteredPages = Math.ceil(filteredCount / productsPerPage);

                // Jika hasil search kosong
                if (filteredCount === 0) {
                    $pagination.empty();
                    return;
                }

                // Setup pagination ulang untuk hasil filter
                $pagination.empty();
                $pagination.append(`
                <li class="page-item">
                    <a href="#" class="page-link" id="prev-page">&laquo;</a>
                </li>
            `);
                for (let i = 1; i <= filteredPages; i++) {
                    $pagination.append(`
                    <li class="page-item" data-page="${i}">
                        <a href="#" class="page-link">${i}</a>
                    </li>
                `);
                }
                $pagination.append(`
                <li class="page-item">
                    <a href="#" class="page-link" id="next-page">&raquo;</a>
                </li>
            `);

                currentPage = 1;
                // Show only first page of filtered results
                $products.hide();
                $visibleProducts.slice(0, productsPerPage).show();

                $pagination.find('li').removeClass('active');
                $pagination.find('li[data-page="1"]').addClass('active');
            });

            $('.nav-link').on('click', function() {
                const categoryId = $(this).attr('id').split('-')[1];
                $('#product-list .card-product').each(function() {
                    const productCategory = $(this).data('category');
                    $(this).toggle(productCategory == categoryId || $('#search-product').val() ===
                        '');
                });
            });

            $('#amount-paid').on('input', function() {
                const amountPaid = $(this).val();
                $(this).val(formatInputRupiah(amountPaid));

                const total = parseFloat($('#total-payment').text().replace(/[^\d]/g, ''));
                const formattedAmountPaid = parseFloat(amountPaid.replace(/[^\d]/g, '')) || 0;

                const change = formattedAmountPaid - total;
                $('#change').text(formatRupiah(change < 0 ? 0 : change));
            });

            function formatInputRupiah(value) {
                const number = parseFloat(value.replace(/[^\d]/g, ''));
                return isNaN(number) ? '0' : formatRupiah(number);
            }

            $('#pay-button').on('click', function() {
                const customerName = $('#customer-name').val();
                if (!customerName) {
                    Swal.fire({
                        title: "Ooops!",
                        text: "Nama pelanggan harus diisi!",
                        icon: "error"
                    });
                    return;
                }

                const paymentMethod = $('input[name="payment-method"]:checked').val();
                if (!paymentMethod) {
                    Swal.fire({
                        title: "Ooops!",
                        text: "Pilih metode pembayaran!",
                        icon: "error"
                    });
                    return;
                }

                const cartItems = [];
                let totalAmount = 0;

                $('#cart-items tr').each(function() {
                    const productName = $(this).find('td:first').text();
                    const quantity = parseInt($(this).find('span').text());
                    const price = parseFloat($(this).find('td:nth-child(3)').text().replace(
                        /[^\d]/g, ''));
                    const total = price * quantity;
                    const productId = $(this).data('id');

                    cartItems.push({
                        product_id: productId,
                        quantity: quantity,
                        price: price
                    });

                    totalAmount += total;
                });

                const transactionData = {
                    customer_name: customerName,
                    payment_method: paymentMethod,
                    total_amount: totalAmount,
                    products: cartItems
                };
                console.log(transactionData);

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Proses ini tidak dapat dibatalkan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, lanjutkan pembayaran!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/pos/store',
                            method: 'POST',
                            data: transactionData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Pembayaran Berhasil!",
                                    text: "Transaksi Anda telah diproses.",
                                    icon: "success"
                                }).then(() => {
                                    printReceipt(response.transaction);

                                    $('#cart-items').empty();
                                    $('#total-payment').text(formatRupiah(0));
                                    $('#amount-paid').val('');
                                    $('#change').text(formatRupiah(0));
                                    $('#customer-name').val('');
                                    $('input[name="payment-method"]').prop(
                                        'checked', false);
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Terjadi kesalahan saat memproses transaksi.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            function printReceipt(transaction) {

                const iframe = document.createElement('iframe');
                iframe.style.position = 'absolute';
                iframe.style.top = '-10000px';
                document.body.appendChild(iframe);

                const doc = iframe.contentWindow.document;
                const receiptHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struk Pembayaran</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    margin: 0;
                    padding: 0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header h2 {
                    margin: 0;
                    font-size: 16px;
                }
                .header p {
                    margin: 0;
                }
                .detail {
                    margin-bottom: 15px;
                    padding: 0 10px;
                }
                .items {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .items th, .items td {
                    border: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                }
                .items th {
                    background-color: #f5f5f5;
                }
                .total {
                    text-align: right;
                    margin: 0 10px;
                    font-size: 14px;
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>STRUK PEMBAYARAN</h2>
                <p>Nota: ${transaction.nota_number}</p>
                <p>Tanggal: ${transaction.transaction_date}</p>
            </div>
            <div class="detail">
                <p><strong>Pelanggan:</strong> ${transaction.customer_name || 'Umum'}</p>
                <p><strong>Metode Pembayaran:</strong> ${transaction.payment_method}</p>
            </div>
            <table class="items">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${transaction.products.map(item => `
                                                <tr>
                                                    <td>${item.product_name}</td>
                                                    <td>${item.quantity}</td>
                                                    <td>${formatRupiah(item.price)}</td>
                                                    <td>${formatRupiah(item.price * item.quantity)}</td>
                                                </tr>
                                            `).join('')}
                </tbody>
            </table>
            <div class="total">
                Total: ${formatRupiah(transaction.total_amount)}
            </div>
            <div class="footer">
                <p>Terima kasih telah berbelanja</p>
            </div>
        </body>
        </html>
    `;

                doc.open();
                doc.write(receiptHTML);
                doc.close();

                iframe.contentWindow.focus();
                iframe.contentWindow.print();

                setTimeout(() => document.body.removeChild(iframe), 1000);
            }


            $('#cancel-button').on('click', function() {
                $('#cart-items').empty();
                $('#total-payment').text(formatRupiah(0));
                $('#amount-paid').val('');
                $('#change').text(formatRupiah(0));
            });
        });
    </script>
@endsection
