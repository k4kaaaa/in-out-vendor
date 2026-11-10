<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $transactions = Transaction::where('product_id', $product->id)
            ->with(['vendor'])
            ->latest()
            ->take(10)
            ->get();

        $stockMovements = $product->stockMovements()
            ->with(['transaction'])
            ->latest()
            ->take(5)
            ->get();

        $totalIn = Transaction::where('product_id', $product->id)
            ->where('type', 'in')
            ->sum('qty');
        $totalOut = Transaction::where('product_id', $product->id)
            ->where('type', 'out')
            ->sum('qty');

        return view('products.show', compact(
            'product',
            'transactions',
            'stockMovements',
            'totalIn',
            'totalOut'
        ));
    }

    public function create()
    {
        $products = Product::all();
        return view('products.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:50'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'in:pcs,box,roll,rim,kg'],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $product = Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'sku' => $request->sku,
            'current_stock' => $request->current_stock,
            'unit' => $request->unit ?? 'pcs',
            'min_stock' => $request->min_stock ?? 5,
            'image' => $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $products = Product::all();
        return view('products.edit', compact('product', 'products'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:50'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'in:pcs,box,roll,rim,kg'],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'sku' => $request->sku,
            'current_stock' => $request->current_stock,
            'unit' => $request->unit ?? 'pcs',
            'min_stock' => $request->min_stock ?? 5,
            'image' => $request->hasFile('image') ? $request->file('image')->store('products', 'public') : $product->image,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }
}