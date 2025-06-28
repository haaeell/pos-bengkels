<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuk = BarangMasuk::with(['product', 'supplier'])->get();
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('barang_masuk.index', compact('barangMasuk', 'products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|integer',
            'tanggal' => 'required|date',
        ]);

        BarangMasuk::create($request->all());

        $product = Product::find($request->product_id);
        $product->quantity += $request->jumlah;
        $product->save();

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil!');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);

        if ($barangMasuk->product_id != $request->product_id) {
            $oldProduct = $barangMasuk->product;
            $oldProduct->quantity -= $barangMasuk->jumlah;
            $oldProduct->save();

            $newProduct = Product::findOrFail($request->product_id);
            $newProduct->quantity += $request->jumlah;
            $newProduct->save();
        } else {
            $product = $barangMasuk->product;
            $stokAdjustment = $request->jumlah - $barangMasuk->jumlah;
            $product->quantity += $stokAdjustment;
            $product->save();
        }

        // Update data barang masuk
        $barangMasuk->update($request->all());

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diperbarui dan stok disesuaikan.');
    }


    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $product = $barangMasuk->product;

        $product->quantity -= $barangMasuk->jumlah;
        $product->save();

        $barangMasuk->delete();

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus dan stok dikurangi.');
    }
}
