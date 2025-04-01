<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required',
            'price' => 'required',
            'satuan' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('public/products');
        $imagePath = Storage::url($imagePath);

        Product::create([
            'code' => $request->code,
            'name' => $request->name,
            'quantity' => 0,
            'price' => clearRupiah($request->price),
            'satuan' => $request->satuan,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required',
            'price' => 'required',
            'satuan' => 'required',
            'description' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public/products/' . basename($product->image));
            }
            $imagePath = $request->file('image')->store('public/products');
            $imagePath = Storage::url($imagePath);
        } else {
            $imagePath = $product->image; 
        }

        $product->update([
            'code' => $request->code,
            'name' => $request->name,
            'quantity' => $product->quantity,
            'price' => clearRupiah($request->price),
            'satuan' => $request->satuan,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
