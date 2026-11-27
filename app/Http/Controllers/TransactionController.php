<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $transactions = Transaction::with('product', 'vendor')->latest()->get();
        return view('transactions.index', compact('products', 'transactions'));
    }

    public function create()
    {
        $products = Product::all();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required', 'in:in,out'],
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'numeric', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'photo_delivery' => ['nullable', 'image', 'max:2048'],
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

            $qtyBefore = $product->current_stock;

            if ($validated['type'] === 'out' && $product->current_stock < $validated['qty']) {
                throw ValidationException::withMessages([
                    'qty' => 'Stok tidak mencukupi',
                ]);
            }

            $transaction = Transaction::create([
                'date' => $validated['date'],
                'type' => $validated['type'],
                'product_id' => $validated['product_id'],
                'vendor_id' => null,
                'qty' => $validated['qty'],
                'customer_name' => $validated['customer_name'] ?? null,
                'receiver_name' => $validated['receiver_name'] ?? null,
                'photo_product' => $request->file('image') ? $request->file('image')->store('transactions', 'public') : null,
                'photo_delivery' => $request->file('photo_delivery') ? $request->file('photo_delivery')->store('transactions', 'public') : null,
            ]);

            $qtyAfter = ($validated['type'] === 'in')
                ? $product->current_stock + $validated['qty']
                : $product->current_stock - $validated['qty'];

            $product->update(['current_stock' => $qtyAfter]);

            StockMovement::create([
                'product_id' => $product->id,
                'transaction_id' => $transaction->id,
                'type' => $validated['type'],
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyAfter,
                'date' => now(),
            ]);

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil disimpan');
        });
    }

    public function edit(Transaction $transaction)
    {
        $products = Product::all();
        return view('transactions.edit', compact('transaction', 'products'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required', 'in:in,out'],
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'numeric', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'photo_delivery' => ['nullable', 'image', 'max:2048'],
        ]);

        return DB::transaction(function () use ($request, $validated, $transaction) {
            $product = Product::where('id', $transaction->product_id)->lockForUpdate()->firstOrFail();
            $oldQty = $transaction->qty;
            $oldType = $transaction->type;

            $oldQtyAfter = ($oldType === 'in')
                ? $product->current_stock - $oldQty
                : $product->current_stock + $oldQty;

            $product->update(['current_stock' => $oldQtyAfter]);
            $transaction->stockMovement()->delete();

            $product->refresh();
            $qtyBefore = $product->current_stock;

            if ($validated['type'] === 'out' && $product->current_stock < $validated['qty']) {
                throw ValidationException::withMessages([
                    'qty' => 'Stok tidak mencukupi',
                ]);
            }

            $transaction->update([
                'date' => $validated['date'],
                'type' => $validated['type'],
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'customer_name' => $validated['customer_name'] ?? null,
                'receiver_name' => $validated['receiver_name'] ?? null,
                'photo_product' => $request->file('image') ? $request->file('image')->store('transactions', 'public') : $transaction->photo_product,
                'photo_delivery' => $request->file('photo_delivery') ? $request->file('photo_delivery')->store('transactions', 'public') : $transaction->photo_delivery,
            ]);

            $qtyAfter = ($validated['type'] === 'in')
                ? $product->current_stock + $validated['qty']
                : $product->current_stock - $validated['qty'];

            $product->update(['current_stock' => $qtyAfter]);

            StockMovement::create([
                'product_id' => $product->id,
                'transaction_id' => $transaction->id,
                'type' => $validated['type'],
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyAfter,
                'date' => now(),
            ]);

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil diperbarui');
        });
    }

    public function destroy(Transaction $transaction)
    {
        $stockMovement = $transaction->stockMovement;
        if ($stockMovement) {
            $stockMovement->delete();
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus');
    }
}