<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class riwayatTransaksiController extends Controller
{
    public function dailyTransactions(Request $request)
    {
        $filterDate = $request->input('filter_date', Carbon::now()->toDateString());
        $transactions = Transaction::whereDate('transaction_date', $filterDate)
            ->orderBy('transaction_date', 'desc')
            ->with(['products' => function ($query) {
                $query->withPivot('quantity', 'price');
            }])
            ->get();
        return view('transaksiHarian', compact('transactions'));
    }
    public function monthlyTransactions(Request $request)
    {
        $filterMonth = $request->input('filter_month', Carbon::now()->format('Y-m'));

        $filterYear = Carbon::parse($filterMonth)->year;
        $filterMonth = Carbon::parse($filterMonth)->month;

        $transactions = Transaction::whereMonth('transaction_date', $filterMonth)
            ->whereYear('transaction_date', $filterYear)
            ->orderBy('transaction_date', 'desc')
            ->with(['products' => function ($query) {
                $query->withPivot('quantity', 'price');
            }])
            ->get();
        return view('transaksiBulanan', compact('transactions'));
    }

    public function sales(Request $request)
    {
        $filterDate = $request->input('filter_date', Carbon::now()->toDateString());

        $transactions = Transaction::whereDate('transaction_date', $filterDate)
            ->orderBy('transaction_date', 'desc')
            ->with(['products' => function ($query) {
                $query->withPivot('quantity', 'price');
            }])
            ->get();

        $dailySales = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->products as $product) {
                $productId = $product->id;
                if (!isset($dailySales[$productId])) {
                    $dailySales[$productId] = [
                        'name' => $product->name,
                        'total_quantity' => 0,
                    ];
                }
                $dailySales[$productId]['total_quantity'] += $product->pivot->quantity;
            }
        }

        $filterMonth = $request->input('filter_month', Carbon::now()->format('Y-m'));
        $filterYear = Carbon::parse($filterMonth)->year;
        $filterMonth = Carbon::parse($filterMonth)->month;

        $transactions = Transaction::whereMonth('transaction_date', $filterMonth)
            ->whereYear('transaction_date', $filterYear)
            ->orderBy('transaction_date', 'desc')
            ->with(['products' => function ($query) {
                $query->withPivot('quantity', 'price');
            }])
            ->get();

        $monthlySales = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->products as $product) {
                $productId = $product->id;
                if (!isset($monthlySales[$productId])) {
                    $monthlySales[$productId] = [
                        'name' => $product->name,
                        'total_quantity' => 0,
                    ];
                }
                $monthlySales[$productId]['total_quantity'] += $product->pivot->quantity;
            }
        }
        return view('penjualan', compact('dailySales',  'monthlySales'));
    }
}
