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
        <div class="col-lg-8 col-md-7 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <input type="text" class="form-control mb-3" id="search-product" placeholder="Search for products...">
                    <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                        @for ($i = 0; $i < 5; $i++)
                            <button class="nav-link {{ $i === 0 ? 'active' : '' }}" id="category-{{ $i }}-tab"
                                data-bs-toggle="tab" data-bs-target="#nav-category-{{ $i }}" type="button"
                                role="tab" aria-controls="nav-category-{{ $i }}"
                                aria-selected="{{ $i === 0 ? 'true' : 'false' }}">Kategori {{ $i }}</button>
                        @endfor
                    </div>
                    <div class="row d-flex p-3" id="product-list">
                        @for ($i = 0; $i < 30; $i++)
                            <div class="col-lg-3 col-md-4 col-sm-6 card-product" data-category="{{ rand(0, 4) }}"
                                style="cursor: pointer;" data-price="{{ rand(10000, 99909) }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-1">
                                        <div
                                            class="rounded-circle-shape bg-light me-3 rounded-pill d-inline-flex align-items-center justify-content-center">
                                            <iconify-icon icon="solar:card-line-duotone"
                                                class="fs-7 text-primary"></iconify-icon>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Produk {{ $i }}</h6>
                                            <p class="mb-0 d-flex align-items-center gap-1">
                                                {{ formatRupiah(rand(10000, 99909)) }}<i class="ti ti-info-circle"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-5 col-sm-12">
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
            $('.card-product').on('click', function() {
                const productPrice = $(this).data('price');
                const productName = $(this).find('h6').text();
                const existingRow = $('#cart-items').find(`tr:contains(${productName})`);

                if (existingRow.length) {
                    const quantity = existingRow.find('span');
                    quantity.text(parseInt(quantity.text()) + 1);
                } else {
                    $('#cart-items').append(`
                        <tr>
                            <td>${productName}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-outline-secondary btn-sm decrease">-</button>
                                    <span class="mx-2">1</span>
                                    <button class="btn btn-outline-secondary btn-sm increase">+</button>
                                </div>
                            </td>
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
                const quantity = $(this).siblings('span');
                quantity.text(parseInt(quantity.text()) + 1);
                updateTotal();
            });

            $('#cart-items').on('click', '.decrease', function() {
                const quantity = $(this).siblings('span');
                const newQuantity = parseInt(quantity.text()) - 1;
                if (newQuantity >= 1) {
                    quantity.text(newQuantity);
                    updateTotal();
                }
            });

            $('#cart-items').on('click', '.remove', function() {
                $(this).closest('tr').remove();
                updateTotal();
            });

            function updateTotal() {
                let total = 0;
                $('#cart-items tr').each(function() {
                    const price = parseFloat($(this).find('td:nth-child(3)').text().replace(/[^\d]/g, ''));
                    const quantity = parseInt($(this).find('span').text());
                    total += price * quantity;
                });
                $('#total-payment').text(formatRupiah(total));
            }

            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('#search-product').on('input', function() {
                const query = $(this).val().toLowerCase();
                $('#product-list .card-product').each(function() {
                    const productName = $(this).find('h6').text().toLowerCase();
                    $(this).toggle(productName.includes(query));
                });
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
                        Swal.fire({
                            title: "Pembayaran Berhasil!",
                            text: "Transaksi Anda telah diproses.",
                            icon: "success"
                        });
                    }
                });
            });


            $('#cancel-button').on('click', function() {
                $('#cart-items').empty();
                $('#total-payment').text(formatRupiah(0));
                $('#amount-paid').val('');
                $('#change').text(formatRupiah(0));
            });
        });
    </script>
@endsection
