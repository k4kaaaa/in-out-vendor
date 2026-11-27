<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor = Vendor::create([
            'name' => $request->name,
            'address' => $request->address,
            'contact' => $request->contact,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil ditambahkan');
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor->update([
            'name' => $request->name,
            'address' => $request->address,
            'contact' => $request->contact,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil diperbarui');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus');
    }
}