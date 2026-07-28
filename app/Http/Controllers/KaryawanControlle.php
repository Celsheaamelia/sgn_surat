<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $karyawanList = Karyawan::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%")
                  ->orWhere('jabatan', 'like', "%{$request->search}%");
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('karyawan.index', compact('karyawanList'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Karyawan::create($data);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $data = $this->validateData($request, $karyawan->id);

        $karyawan->update($data);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }

    /**
     * Dipakai halaman "Buat Kontrak" untuk autofill data karyawan lewat AJAX.
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $result = Karyawan::query()
            ->where('status_karyawan', 'Aktif')
            ->when($q, function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                      ->orWhere('nik', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(15)
            ->get(['id', 'nik', 'no_ktp', 'nama', 'jabatan', 'departemen', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'status_perkawinan', 'alamat']);

        return response()->json($result);
    }

    private function validateData(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'nik'                 => 'required|string|max:30|unique:karyawans,nik' . ($ignoreId ? ",{$ignoreId}" : ''),
            'no_ktp'              => 'nullable|string|max:30',
            'nama'                => 'required|string|max:150',
            'jabatan'             => 'nullable|string|max:100',
            'departemen'          => 'nullable|string|max:100',
            'tempat_lahir'        => 'nullable|string|max:100',
            'tanggal_lahir'       => 'nullable|date',
            'jenis_kelamin'       => 'nullable|in:Laki-laki,Perempuan',
            'agama'               => 'nullable|string|max:30',
            'status_perkawinan'   => 'nullable|string|max:30',
            'alamat'              => 'nullable|string',
            'no_hp'               => 'nullable|string|max:30',
            'email'               => 'nullable|email|max:150',
            'tanggal_mulai_kerja' => 'nullable|date',
            'status_karyawan'     => 'nullable|in:Aktif,Nonaktif',
        ]);
    }
}
