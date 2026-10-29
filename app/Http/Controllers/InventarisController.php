<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventarisController extends Controller
{
    /**
     * Tampilkan katalog seluruh Inventaris dengan Eager Loading (with('kategori'))
     * guna meniadakan masalah N+1 Query Problem secara mutlak.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $kategoriId = $request->query('kategori_id');

        // Menggunakan Eager Loading with('kategori') untuk optimasi performa kueri
        $query = Inventaris::with('kategori');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('categories', 'like', "%{$search}%")
                  ->orWhere('equipments', 'like', "%{$search}%")
                  ->orWhere('loan_requests', 'like', "%{$search}%");
            });
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $inventariss = $query->latest()->paginate(10)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Statistik ringkas untuk dashboard antarmuka
        $totalBuku = Inventaris::count();
        $totalStok = Inventaris::sum('stok');
        $totalKategori = Kategori::count();

        return view('inventaris.index', compact('inventariss', 'kategoris', 'search', 'kategoriId', 'totalBuku', 'totalStok', 'totalKategori'));
    }

    /**
     * Tampilkan formulir untuk menambahkan entri inventaris baru.
     */
    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.create', compact('kategoris'));
    }

    /**
     * Simpan inventaris baru ke basis data dengan validasi ketat sisi server.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentYear = (int) date('Y');

        $validated = $request->validate([
            'categories'         => ['required', 'string', 'size:13', 'regex:/^[0-9]{13}$/', 'unique:inventariss,categories'],
            'equipments'        => ['required', 'string', 'min:5', 'max:255'],
            'loan_requests'      => ['required', 'string', 'min:3', 'max:150'],
            'kategori_id'  => ['required', 'exists:kategoris,id'],
            'stok'         => ['required', 'integer', 'min:0'],
        ], [
            'categories.required'         => 'Nomor categories wajib diisi.',
            'categories.size'             => 'Nomor categories harus tepat berjumlah 13 digit.',
            'categories.regex'            => 'Nomor categories hanya boleh memuat deretan angka numerik.',
            'categories.unique'           => 'Nomor categories tersebut sudah terdaftar di basis data.',
            'equipments.required'        => 'equipments inventaris wajib diisi.',
            'equipments.min'             => 'equipments inventaris minimal harus memiliki 5 karakter.',
            'loan_requests.required'      => 'loan_requests wajib diisi.',
            'loan_requests.min'           => 'loan_requests minimal 3 karakter.',
            'loan_requests.required'     => 'loan_requests wajib diisi.',
            'loan_requests.min'          => 'loan_requests minimal 3 karakter.',
            'kategori_id.required'  => 'Kategori inventaris wajib dipilih.',
            'kategori_id.exists'    => 'Kategori yang dipilih tidak valid atau belum terdaftar.',
            'stok.required'         => 'Jumlah stok inventaris wajib diisi.',
            'stok.min'              => 'Stok inventaris tidak boleh bernilai negatif (minimal 0).',
            'sinopsis.max'          => 'Sinopsis tidak boleh melebihi 1000 karakter.',
        ]);

        Inventaris::create($validated);

        return redirect()->route('inventaris.index')
            ->with('success', "Inventaris '{$validated['categories']}' berhasil ditambahkan ke katalog!");
    }

    /**
     * Tampilkan detail spesifik inventaris beserta kategori terkait melalui Implicit Route Model Binding.
     */
    public function show(Inventaris $inventaris): View
    {
        $inventaris->load('kategori');

        return view('inventaris.show', compact('inventaris'));
    }

    /**
     * Tampilkan formulir edit inventaris dengan data eksisting melalui Implicit Route Model Binding.
     */
    public function edit(Inventaris $inventaris): View
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('inventaris.edit', compact('inventaris', 'kategoris'));
    }

    /**
     * Perbarui data inventaris di basis data dengan validasi server-side toleran ID sendiri.
     */
    public function update(Request $request, Inventaris $inventaris): RedirectResponse
    {
        $currentYear = (int) date('Y');

        $validated = $request->validate([
            'categories'         => [
                'required',
                'string',
                'size:13',
                'regex:/^[0-9]{13}$/',
                Rule::unique('inventariss', 'categories')->ignore($categories->id),
            ],
            'categories'         => ['required', 'string', 'size:13', 'regex:/^[0-9]{13}$/', 'unique:inventariss,categories'],
            'equipments'        => ['required', 'string', 'min:5', 'max:255'],
            'loan_requests'      => ['required', 'string', 'min:3', 'max:150'],
            'kategori_id'  => ['required', 'exists:kategoris,id'],
            'stok'         => ['required', 'integer', 'min:0'],
        ], [
            'categories.required'         => 'Nomor categories wajib diisi.',
            'categories.size'             => 'Nomor categories harus tepat berjumlah 13 digit.',
            'categories.regex'            => 'Nomor categories hanya boleh memuat deretan angka numerik.',
            'categories.unique'           => 'Nomor categories tersebut sudah terdaftar di basis data.',
            'equipments.required'        => 'equipments inventaris wajib diisi.',
            'equipments.min'             => 'equipments inventaris minimal harus memiliki 5 karakter.',
            'loan_requests.required'      => 'loan_requests wajib diisi.',
            'loan_requests.min'           => 'loan_requests minimal 3 karakter.',
            'loan_requests.required'     => 'loan_requests wajib diisi.',
            'loan_requests.min'          => 'loan_requests minimal 3 karakter.',
            'kategori_id.required'  => 'Kategori inventaris wajib dipilih.',
            'kategori_id.exists'    => 'Kategori yang dipilih tidak valid atau belum terdaftar.',
            'stok.required'         => 'Jumlah stok inventaris wajib diisi.',
            'stok.min'              => 'Stok inventaris tidak boleh bernilai negatif (minimal 0).',
            'sinopsis.max'          => 'Sinopsis tidak boleh melebihi 1000 karakter.',
        ]);

        $inventaris->update($validated);

        return redirect()->route('inventaris.index')
            ->with('success', "Informasi inventaris '{$inventaris->judul}' berhasil diperbarui!");
    }

    /**
     * Hapus entri inventaris dari basis data.
     */
    public function destroy(Inventaris $inventaris): RedirectResponse
    {
        $categories =inventaris->categories;
        $inventaris->delete();

        return redirect()->route('inventaris.index')
            ->with('success', "Inventaris '{$categories}' berhasil dihapus dari sistem peminjaman!");
    }
}
