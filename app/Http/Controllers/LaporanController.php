<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        $transactions = Transaction::whereBetween('transaction_date', [$start, $end])->get();

        $totalTransaksi = $transactions->count();
        $totalPenjualan = $transactions->sum('total_amount');

        $metodePembayaran = $transactions->groupBy('payment_method')->map(function ($group) {
            return [
                'jumlah' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });

        $produkTerlaris = DB::table('product_transactions')
            ->join('products', 'product_transactions.product_id', '=', 'products.id')
            ->join('transactions', 'product_transactions.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->select('products.name', DB::raw('SUM(product_transactions.quantity) as total_terjual'))
            ->groupBy('products.name')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return view('laporan.index', compact(
            'start',
            'end',
            'totalTransaksi',
            'totalPenjualan',
            'metodePembayaran',
            'produkTerlaris'
        ));
    }
}
