@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Transaksi bulanan</h2>
            <div class="col-md-3">
                <form method="GET" action="{{ route('transaksi.bulanan') }}" class="d-flex align-items-center">
                    <input type="month" name="filter_month" id="filter-month" class="form-control me-2"
                        value="{{ request('filter_month', \Carbon\Carbon::now()->format('Y-m')) }}">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        <div class="card-body">
            @if ($transactions->isEmpty())
                <p>Belum ada transaksi untuk bulan ini.</p>
            @else
                <table class="table table-bordered" id="datatables">
                    <thead>
                        <tr>
                            <th>Nota</th>
                            <th>Tanggal</th>
                            <th>Metode Pembayaran</th>
                            <th>Total</th>
                            <th>Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->nota_number }}</td>
                                <td>{{ formatTanggal($transaction->transaction_date) }}</td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td>{{ formatRupiah($transaction->total_amount) }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#productModal-{{ $transaction->id }}">
                                        Lihat Produk
                                    </button>

                                    <div class="modal fade" id="productModal-{{ $transaction->id }}" tabindex="-1"
                                        aria-labelledby="productModalLabel-{{ $transaction->id }}" aria-hidden="true">
                                        <div class="modal-dialog  modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="productModalLabel-{{ $transaction->id }}">
                                                        Detail Produk</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Nama Produk</th>
                                                                <th>Jumlah</th>
                                                                <th>Harga Satuan</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($transaction->products as $product)
                                                                <tr>
                                                                    <td>{{ $product->name }}</td>
                                                                    <td>{{ $product->pivot->quantity }}</td>
                                                                    <td>Rp
                                                                        {{ number_format($product->pivot->price, 0, ',', '.') }}
                                                                    </td>
                                                                    <td>Rp
                                                                        {{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
