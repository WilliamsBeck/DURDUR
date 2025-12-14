<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupplierController extends Controller
{
    /**
     * Menampilkan daftar supplier yang aktif (tanpa Soft Delete).
     */
    public function index(Request $request): View
    {
        // HANYA AMBIL DATA YANG AKTIF
        $suppliersQuery = Supplier::withoutTrashed();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $suppliersQuery->where('supplier_name', 'like', '%' . $searchTerm . '%')
                           ->orWhere('pic_supplier', 'like', '%' . $searchTerm . '%')
                           ->orWhere('supplier_email', 'like', '%' . $searchTerm . '%');
        }

        $suppliers = $suppliersQuery->latest()->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_name'    => 'required|min:3',
            'pic_supplier'     => 'nullable|string',
            'supplier_email'   => 'nullable|email',
            'supplier_phone'   => 'nullable|numeric',
            'supplier_address' => 'nullable|string',
        ]);
        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with(['success' => 'Data Supplier Berhasil Disimpan!']);
    }

    public function show(Supplier $supplier): View
    {
        // show tetap berfungsi meskipun supplier sudah diarsip
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $request->validate([
            'supplier_name'    => 'required|min:3',
            'pic_supplier'     => 'nullable|string',
            'supplier_email'   => 'nullable|email',
            'supplier_phone'   => 'nullable|numeric',
            'supplier_address' => 'nullable|string',
        ]);
        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with(['success' => 'Data Supplier Berhasil Diubah!']);
    }

    /**
     * Mengarsipkan (Soft Delete) supplier.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete(); // Ini akan mengisi kolom deleted_at
        return redirect()->route('suppliers.index')->with(['success' => 'Data Supplier Berhasil Diarsipkan!']);
    }

    // ================= ARCHIVE AND RESTORE FUNCTIONS =================

    /**
     * Menampilkan daftar supplier yang diarsip (Soft Deleted).
     */
    public function archived(Request $request): View
    {
        // HANYA AMBIL DATA YANG DIARSIP
        $suppliersQuery = Supplier::onlyTrashed();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $suppliersQuery->where('supplier_name', 'like', '%' . $searchTerm . '%')
                           ->orWhere('pic_supplier', 'like', '%' . $searchTerm . '%')
                           ->orWhere('supplier_email', 'like', '%' . $searchTerm . '%');
        }

        $suppliers = $suppliersQuery->latest()->paginate(10);

        return view('suppliers.archived', compact('suppliers'));
    }

    /**
     * Memulihkan supplier yang di-soft delete (Restore).
     */
    public function restore(string $id): RedirectResponse
    {
        // Mencari data termasuk yang sudah di-soft delete
        $supplier = Supplier::withTrashed()->findOrFail($id); 
        $supplier->restore();
        
        return redirect()->route('suppliers.archived')->with(['success' => 'Data Supplier Berhasil Dipulihkan!']);
    }
}