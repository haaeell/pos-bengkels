@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="mb-3 ">Penjualan Harian </h4>
                    <div class="col-md-3">
                        <form method="GET" action="{{ route('penjualan') }}" class="d-flex align-items-center">
                            <input type="date" name="filter_date" id="filter-date" class="form-control me-2"
                                value="{{ request('filter_date', \Carbon\Carbon::now()->toDateString()) }}">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="datatables">
                            <thead>
                                <tr class="text-center table-primary">
                                    <th>Nama Produk</th>
                                    <th>Jumlah Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dailySales as $sale)
                                    <tr>
                                        <td>{{ $sale['name'] }}</td>
                                        <td>{{ $sale['total_quantity'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="mb-3 ">Penjualan bulanan</h4>
                    <div class="col-md-3">
                        <form method="GET" action="{{ route('penjualan') }}" class="d-flex align-items-center">
                            <input type="month" name="filter_month" id="filter-month" class="form-control me-2"
                                value="{{ request('filter_month', \Carbon\Carbon::now()->format('Y-m')) }}">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="datatables2">
                            <thead>
                                <tr class="text-center table-primary">
                                    <th>Nama Produk</th>
                                    <th>Jumlah Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlySales as $sale)
                                    <tr>
                                        <td>{{ $sale['name'] }}</td>
                                        <td>{{ $sale['total_quantity'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
