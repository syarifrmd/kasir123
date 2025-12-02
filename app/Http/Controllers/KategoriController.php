<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;

class KategoriController extends Controller
{
    protected function filePath(): string
    {
        return storage_path('app/kategori.json');
    }

    protected function readAll(): array
    {
        $path = $this->filePath();
        if (is_file($path)) {
            $data = json_decode(file_get_contents($path), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    protected function writeAll(array $data): void
    {
        ksort($data);
        file_put_contents($this->filePath(), json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }

    public function index()
    {
        $defaults = Barang::KATEGORI;
        $extra = $this->readAll();
        // Build combined list with source flag
        $combined = collect($defaults)->map(fn($nama, $kode) => [
            'kode' => $kode,
            'nama' => $nama,
            'source' => 'default',
        ])->merge(collect($extra)->map(fn($nama, $kode) => [
            'kode' => $kode,
            'nama' => $nama,
            'source' => 'custom',
        ]))->sortBy('kode')->values();

        return view('kategori.index', [
            'list' => $combined,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required','string','size:2','regex:/^[A-Z]{2}$/'],
            'nama' => ['required','string','max:100'],
        ]);

        $all = array_change_key_case(Barang::kategoriOptions(), CASE_UPPER);
        if (isset($all[$data['kode']])) {
            return back()->with('error', 'Kode kategori sudah ada.');
        }

        $extra = $this->readAll();
        $extra[$data['kode']] = $data['nama'];
        $this->writeAll($extra);

        return redirect()->route('kategori.index')->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, string $kode)
    {
        $kode = strtoupper($kode);
        $data = $request->validate([
            'nama' => ['required','string','max:100'],
        ]);

        // Only allow update for custom entries
        if (array_key_exists($kode, Barang::KATEGORI)) {
            return back()->with('error', 'Kategori default tidak dapat diubah.');
        }

        $extra = $this->readAll();
        if (!array_key_exists($kode, $extra)) {
            return back()->with('error', 'Kategori tidak ditemukan.');
        }
        $extra[$kode] = $data['nama'];
        $this->writeAll($extra);
        return redirect()->route('kategori.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(string $kode)
    {
        $kode = strtoupper($kode);
        if (array_key_exists($kode, Barang::KATEGORI)) {
            return back()->with('error', 'Kategori default tidak dapat dihapus.');
        }
        $extra = $this->readAll();
        unset($extra[$kode]);
        $this->writeAll($extra);
        return redirect()->route('kategori.index')->with('success', 'Kategori dihapus.');
    }
}
