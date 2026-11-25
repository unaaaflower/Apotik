<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObatController extends Controller
{
    private Produk $produk;

    public function __construct()
    {
        $this->produk = new Produk();
    }

    /**
     * Menampilkan halaman Kelola Obat
     * sekaligus menyediakan data JSON untuk JavaScript.
     */
    public function index(Request $request)
    {
        $keyword = $request->query('cari', '');
        $kategori = $request->query('kategori', '');

        $daftarObat = $this->produk->cariUntukAdmin(
            $keyword,
            $kategori
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $daftarObat,
            ]);
        }

        return view('admin.obat', [
            'daftarObat' => $daftarObat,
        ]);
    }

    /**
     * Menyimpan obat baru.
     */
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $id = $this->produk->tambah($data);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil ditambahkan.',
            'data' => [
                'id' => $id,
            ],
        ], 201);
    }

    /**
     * Mengambil satu data obat untuk proses edit.
     */
    public function show(int $id)
    {
        $obat = collect($this->produk->semuaUntukAdmin())
            ->firstWhere('id', $id);

        if (!$obat) {
            return response()->json([
                'success' => false,
                'message' => 'Data obat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $obat,
        ]);
    }

    /**
     * Memperbarui data obat.
     */
    public function update(Request $request, int $id)
    {
        $obat = collect($this->produk->semuaUntukAdmin())
            ->firstWhere('id', $id);

        if (!$obat) {
            return response()->json([
                'success' => false,
                'message' => 'Data obat tidak ditemukan.',
            ], 404);
        }

        $data = $this->validasi($request, $id);

        $this->produk->ubah($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Data obat berhasil diperbarui.',
        ]);
    }

    /**
     * Menghapus obat.
     */
    public function destroy(int $id)
    {
        $obat = collect($this->produk->semuaUntukAdmin())
            ->firstWhere('id', $id);

        if (!$obat) {
            return response()->json([
                'success' => false,
                'message' => 'Data obat tidak ditemukan.',
            ], 404);
        }

        $this->produk->hapus($id);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil dihapus.',
        ]);
    }

    /**
     * Validasi data obat.
     */
    private function validasi(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('produk', 'nama')->ignore($id),
            ],
            'kategori' => [
                'required',
                Rule::in([
                    'Obat Resep',
                    'Obat Bebas',
                    'Vitamin & Suplemen',
                    'Perawatan Diri',
                    'Alat Kesehatan',
                ]),
            ],
            'satuan' => [
                'required',
                'string',
                'max:20',
            ],
            'harga' => [
                'required',
                'integer',
                'min:0',
            ],
            'stok' => [
                'required',
                'integer',
                'min:0',
            ],
            'ket' => [
                'nullable',
                'string',
                'max:60',
            ],
            'deskripsi' => [
                'nullable',
                'string',
            ],
            'butuh_resep' => [
                'required',
                'boolean',
            ],
            'aktif' => [
                'required',
                'boolean',
            ],
        ]);

        $data['ket'] = $data['ket'] ?? '';
        $data['deskripsi'] = $data['deskripsi'] ?? '';

        return $data;
    }
}
