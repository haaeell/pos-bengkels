<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('kasir.pos.index', compact('products'));
    }
    public function services()
    {
        $products = Product::all();
        return view('kasir.service.index', compact('products'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $notaNumber = 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);

            $transaction = Transaction::create([
                'nota_number' => $notaNumber,
                'transaction_date' => now(),
                'total_amount' => $request->total_amount,
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'cashier_id' => auth()->id(),
            ]);

            foreach ($request->products as $product) {
                ProductTransaction::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);

                $currentProduct = Product::findOrFail($product['product_id']);

                if ($currentProduct->quantity < $product['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stok produk {$currentProduct->name} tidak mencukupi."
                    ], 400);
                }

                $currentProduct->update([
                    'quantity' => $currentProduct->quantity - $product['quantity'],
                ]);
            }

            DB::commit();

            $transactionData = DB::table('transactions')->where('id', $transaction->id)->first();
            $products = DB::table('product_transactions')
                ->join('products', 'product_transactions.product_id', '=', 'products.id')
                ->where('transaction_id', $transaction->id)
                ->select('products.name as product_name', 'product_transactions.quantity', 'product_transactions.price')
                ->get();

            $receiptData = [
                'nota_number' => $transactionData->nota_number,
                'transaction_date' => \Carbon\Carbon::parse($transactionData->transaction_date)->format('d/m/Y H:i:s'),
                'customer_name' => $request->customer_name,
                'payment_method' => $transactionData->payment_method,
                'total_amount' => $transactionData->total_amount,
                'products' => $products,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created successfully',
                'transaction' => $receiptData
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}
