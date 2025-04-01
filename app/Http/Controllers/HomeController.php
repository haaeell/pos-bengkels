<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $totalPendapatan = Transaction::whereDate('transaction_date', Carbon::today())
            ->sum('total_amount');

        $totalCash = Transaction::whereDate('transaction_date', Carbon::today())
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $totalTransfer = Transaction::whereDate('transaction_date', Carbon::today())
            ->where('payment_method', 'bank_transfer')
            ->sum('total_amount');

        $stokHampirHabis = Product::where('quantity', '<', 5)->get();

        $topProducts = ProductTransaction::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('transaction_date', Carbon::now()->month);
            })
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();
            

        return view('home', compact('totalPendapatan', 'totalCash', 'totalTransfer', 'stokHampirHabis', 'topProducts'));
    }
}
