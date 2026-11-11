<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'user' => $user
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }

    public function products()
    {
        return response()->json([
            'success' => true,
            'data' => Product::all()
        ]);
    }

    public function vendors()
    {
        return response()->json([
            'success' => true,
            'data' => Vendor::all()
        ]);
    }

    public function transactions()
    {
        return response()->json([
            'success' => true,
            'data' => Transaction::with(['product', 'vendor', 'user'])->latest()->get()
        ]);
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'type' => 'required|in:in,out',
            'qty' => 'required|numeric|min:1',
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $transaction = Transaction::create($request->all());
        
        // Update product stock
        $product = Product::find($request->product_id);
        if ($request->type == 'in') {
            $product->increment('current_stock', $request->qty);
        } else {
            $product->decrement('current_stock', $request->qty);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'data' => $transaction
        ]);
    }
}