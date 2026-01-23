@extends('layouts.kasir')
@section('title','Data Vendor/Customer')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Data Kontak</h1>
    <a href="{{ route('vendors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Tambah Kontak</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Tipe</th>
                <th class="px-4 py-3">HP</th>
                <th class="px-4 py-3">Alamat</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($vendors as $v)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $v->name }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded {{ $v->tipe == 'VENDOR' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                        {{ $v->tipe }}
                    </span>
                </td>
                <td class="px-4 py-3">{{ $v->hp }}</td>
                <td class="px-4 py-3">{{ $v->alamat }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('vendors.edit', $v->id) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('vendors.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">
        {{ $vendors->links() }}
    </div>
</div>
@endsection
