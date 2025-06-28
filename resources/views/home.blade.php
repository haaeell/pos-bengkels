@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4>Dashboard</h4>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <h4 class="text-center">Total Pendapatan</h4>
                                            <h6 class="text-center">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <h2>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6>Cash</h6>
                                            <h5>Rp {{ number_format($totalCash, 0, ',', '.') }}</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Transfer</h6>
                                            <h5>Rp {{ number_format($totalTransfer, 0, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        @if ($stokHampirHabis->count() > 0)
                            <div class="alert alert-warning mt-3" role="alert">
                                ⚠️ Beberapa barang hampir habis! Segera lakukan restock agar tidak kehabisan stok.
                            </div>
                        @else
                            <div class="alert alert-success mt-3" role="alert">
                                ✅ Semua stok dalam kondisi aman. Tidak ada barang yang hampir habis.
                            </div>
                        @endif
                        <h5>Stok Hampir Habis</h5>
                        <table class="table rounded" id="datatables">
                            <thead class="table-danger">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-nowrap">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stokHampirHabis as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center">{{ $product->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Barang dengan Penjualan Terbanyak Bulan {{ now()->format('F Y') }}</h5>
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: @json($topProducts->pluck('product.name')->toArray()),
                datasets: [{
                    label: 'Penjualan',
                    data: @json($topProducts->pluck('total_sold')->toArray()),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Penjualan Terbanyak'
                    }
                }
            }
        });
    </script>
@endsection
