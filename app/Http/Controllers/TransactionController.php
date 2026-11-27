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
    public function index(Request $request)
    {
        $products = Product::all();
        $vendors = \App\Models\Vendor::all();
        $query = Transaction::with('product', 'vendor');

        $type = $request->input('type', 'in');
        if ($type !== 'all' && $type) {
            $query->where('type', $type);
        }
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', '%'.$search.'%')
                          ->orWhere('sku', 'like', '%'.$search.'%');
                    })
                  ->orWhere('customer_name', 'like', '%'.$search.'%');
            });
        }
        if ($vendorId = $request->input('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        $transactions = $query->latest()->get();
        return view('transactions.index', compact('products', 'vendors', 'transactions'));
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
            'product_mode' => ['nullable', 'in:existing,new'],
            'product_id' => ['nullable', 'exists:products,id'],
            'new_product_name' => ['nullable', 'string', 'max:255'],
            'new_product_sku' => ['nullable', 'string', 'max:255'],
            'new_product_category' => ['nullable', 'string', 'max:255'],
            'new_product_unit' => ['nullable', 'string', 'max:50'],
            'new_product_min_stock' => ['nullable', 'numeric', 'min:0'],
            'qty' => ['required', 'numeric', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'photo_delivery' => ['nullable', 'image', 'max:2048'],
        ]);

        if (empty($validated['product_id']) && empty($validated['new_product_name'])) {
            throw ValidationException::withMessages([
                'product_id' => 'Pilih barang yang sudah ada atau isi nama barang baru.',
            ]);
        }

        // Simpan file dulu, di luar transaksi DB (file I/O tidak perlu berada dalam transaction)
        $photoProduct = $request->hasFile('image') ? $request->file('image')->store('transactions', 'public') : null;
        $photoDelivery = $request->hasFile('photo_delivery') ? $request->file('photo_delivery')->store('transactions', 'public') : null;

        $attempt = 0;
        do {
            $attempt++;
            try {
                $transaction = DB::transaction(function () use ($validated, $photoProduct, $photoDelivery) {
                    $qty = (float) $validated['qty'];
                    $product = null;

                    if (!empty($validated['product_id'])) {
                        $product = Product::find($validated['product_id']);
                        if (!$product) {
                            throw ValidationException::withMessages([
                                'product_id' => 'Produk dengan ID tersebut tidak ditemukan.',
                            ]);
                        }
                    } else {
                        $product = Product::create([
                            'name' => $validated['new_product_name'],
                            'sku' => $validated['new_product_sku'] ?? null,
                            'category' => $validated['new_product_category'] ?? null,
                            'unit' => $validated['new_product_unit'] ?: 'pcs',
                            'min_stock' => $validated['new_product_min_stock'] ?? 5,
                            'current_stock' => 0,
                        ]);
                    }

                    $qtyBefore = (float) $product->current_stock;

                    if ($validated['type'] === 'out') {
                        // Atomic: only succeeds if enough stock (no FOR UPDATE needed)
                        $affected = DB::table('products')
                            ->where('id', $product->id)
                            ->where('current_stock', '>=', $qty)
                            ->decrement('current_stock', $qty);

                        if ($affected === 0) {
                            throw ValidationException::withMessages([
                                'qty' => 'Stok tidak mencukupi',
                            ]);
                        }
                    } else {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->increment('current_stock', $qty);
                    }

                    $product->refresh();
                    $qtyAfter = (float) $product->current_stock;

                    $trx = Transaction::create([
                        'date' => $validated['date'],
                        'type' => $validated['type'],
                        'product_id' => $product->id,
                        'vendor_id' => null,
                        'qty' => $qty,
                        'customer_name' => $validated['customer_name'] ?? null,
                        'receiver_name' => $validated['receiver_name'] ?? null,
                        'photo_product' => $photoProduct,
                        'photo_delivery' => $photoDelivery,
                    ]);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'transaction_id' => $trx->id,
                        'type' => $validated['type'],
                        'qty_before' => $qtyBefore,
                        'qty_after' => $qtyAfter,
                        'date' => now(),
                    ]);

                    return $trx;
                });
                break; // sukses
            } catch (ValidationException $e) {
                throw $e; // validasi bisnis, tidak perlu retry
            } catch (\Throwable $e) {
                // Gagal (mis. transaction aborted di pooler Neon).
                // Rollback + putus koneksi agar state rusak tidak kembali ke pooler.
                try {
                    DB::rollBack();
                } catch (\Throwable) {
                    // abaikan error rollback
                }
                try {
                    \Illuminate\Support\Facades\DB::purge();
                } catch (\Throwable) {
                    // abaikan
                }

                // Retry 1x di koneksi bersih
                if ($attempt === 1) {
                    continue;
                }

                if (str_contains($e->getMessage(), 'current transaction is aborted')
                    || str_contains($e->getMessage(), '25P02')) {
                    throw ValidationException::withMessages([
                        'product_id' => 'Terjadi masalah koneksi database. Silakan coba lagi sebentar lagi.',
                    ]);
                }
                throw $e;
            }
        } while ($attempt < 2);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan');
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
            $product = Product::findOrFail($transaction->product_id);
            $oldQty = (float) $transaction->qty;
            $oldType = $transaction->type;

            // Undo old effect atomically
            if ($oldType === 'in') {
                DB::table('products')->where('id', $product->id)->decrement('current_stock', $oldQty);
            } else {
                DB::table('products')->where('id', $product->id)->increment('current_stock', $oldQty);
            }
            $transaction->stockMovement()->delete();

            $product->refresh();
            $qtyBefore = (float) $product->current_stock;
            $newQty = (float) $validated['qty'];

            if ($validated['type'] === 'out') {
                $affected = DB::table('products')
                    ->where('id', $product->id)
                    ->where('current_stock', '>=', $newQty)
                    ->decrement('current_stock', $newQty);
                if ($affected === 0) {
                    throw ValidationException::withMessages([
                        'qty' => 'Stok tidak mencukupi',
                    ]);
                }
            } else {
                DB::table('products')
                    ->where('id', $product->id)
                    ->increment('current_stock', $newQty);
            }

            $transaction->update([
                'date' => $validated['date'],
                'type' => $validated['type'],
                'product_id' => $validated['product_id'],
                'qty' => $newQty,
                'customer_name' => $validated['customer_name'] ?? null,
                'receiver_name' => $validated['receiver_name'] ?? null,
                'photo_product' => $request->file('image') ? $request->file('image')->store('transactions', 'public') : $transaction->photo_product,
                'photo_delivery' => $request->file('photo_delivery') ? $request->file('photo_delivery')->store('transactions', 'public') : $transaction->photo_delivery,
            ]);

            $product->refresh();
            $qtyAfter = (float) $product->current_stock;

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