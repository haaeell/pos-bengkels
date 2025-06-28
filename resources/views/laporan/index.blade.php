@extends('layouts.dashboard')

@section('content')
    <style>
        #produkTerlarisChart {
            max-height: 300px;
        }

        .chartjs-render-monitor {
            margin: auto;
        }

        .icon-big {
            font-size: 2rem !important;
        }
    </style>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3>Laporan Bulanan</h3>
                <form method="GET" action="{{ route('laporan.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ request('start_date', $start->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ request('end_date', $end->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="row g-4 justify-content-center text-center">
                    <div class="col-md-4">
                        <div class="card p-4 shadow" style="background-color: #e3f2fd;">
                            <i class="ti ti-shopping-cart fs-1 icon-big mb-2 text-primary"></i>
                            <h5>Jumlah Transaksi</h5>
                            <h3>{{ $totalTransaksi }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 shadow" style="background-color: #e8f5e9;">
                            <i class="ti ti-currency-dollar fs-1 icon-big mb-2 text-success"></i>
                            <h5>Total Penjualan</h5>
                            <h3>{{ formatRupiah($totalPenjualan) }}</h3>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Produk Paling Banyak Terjual</h5>
                            </div>
                            <div class="card-body">
                                @if ($produkTerlaris->isEmpty())
                                    <p>Tidak ada data produk terjual.</p>
                                @else
                                    <div style="max-width: 300px; margin: auto;">
                                        <canvas id="produkTerlarisChart"></canvas>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Ringkasan per Metode Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                @if ($metodePembayaran->isEmpty())
                                    <p>Tidak ada transaksi pada rentang ini.</p>
                                @else
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Metode</th>
                                                <th>Jumlah Transaksi</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($metodePembayaran as $metode => $data)
                                                <tr>
                                                    <td>{{ ucfirst($metode) }}</td>
                                                    <td>{{ $data['jumlah'] }}</td>
                                                    <td>{{ formatRupiah($data['total']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = {!! json_encode($produkTerlaris->pluck('name')) !!};
        const data = {!! json_encode($produkTerlaris->pluck('total_terjual')) !!};

        // Generate warna random untuk setiap slice
        const backgroundColors = labels.map(() => {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, 0.7)`;
        });

        const ctx = document.getElementById('produkTerlarisChart').getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                return `${label}: ${value} terjual`;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
