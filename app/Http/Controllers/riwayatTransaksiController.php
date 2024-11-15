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
            ->with(['products' => function ($query) {
                $query->withPivot('quantity', 'price');
            }])
            ->get();
        return view('transaksiHarian', compact('transactions'));
    }
}
