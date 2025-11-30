<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name','Kasir'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Use Vite's Alpine (already imported in resources/js/app.js) to avoid double init --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        /* Mobile drawer for sidebar when body has class show-mobile-nav */
        @media (max-width: 767px) {
            body.show-mobile-nav aside.kasir-sidebar { display:flex !important; position: fixed; inset: 0 30% 0 0; z-index: 50; }
            body.show-mobile-nav::before { content:''; position:fixed; inset:0; background: rgba(0,0,0,0.45); z-index: 40; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Fixed Sidebar -->
    <aside class="kasir-sidebar hidden md:flex md:flex-col w-56 bg-orange-600 text-white min-h-screen sticky top-0 shadow-lg">
        <div class="px-5 py-5 border-b border-orange-500">
            <h1 class="text-lg font-semibold tracking-wide">Kasir App</h1>
            <p class="text-xs text-orange-100 mt-1">v1.0</p>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 text-sm">
            @php($current = request()->route()?->getName())
            <ul class="space-y-1 px-3">
                <li>
                    <a href="{{ route('dashboard.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'dashboard.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pos.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'pos.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Kasir</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('barang.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'barang.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Data Barang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaksi.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'transaksi.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('stok.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'stok.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Manajemen Stok</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vendors.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'vendors.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Vendor</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('profit.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded transition {{ str_starts_with($current,'profit.') ? 'bg-white/15 font-medium' : 'hover:bg-white/10' }}">
                        <span class="w-2 h-2 rounded-full bg-white/70 group-hover:bg-white"></span>
                        <span>Ringkasan Keuntungan</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="px-4 py-4 text-[10px] text-orange-100 border-t border-orange-500">&copy; {{ date('Y') }} - Kasir</div>
    </aside>
    <!-- Content Wrapper -->
    <div class="flex-1 flex flex-col min-h-screen">
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
                <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded bg-orange-600 text-white" onclick="document.body.classList.toggle('show-mobile-nav')">☰</button>
                <h2 class="font-semibold tracking-wide text-sm text-gray-600">@yield('title','Kasir')</h2>
                <div class="ml-auto flex items-center gap-3 text-xs text-gray-600">
                    <span class="hidden sm:inline">{{ now()->format('d M Y') }}</span>
                    @auth
                        <div class="flex items-center gap-2 px-3 py-1 rounded bg-orange-50 border border-orange-200">
                            <span class="text-orange-700 font-semibold">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Logout?')">
                                @csrf
                                <button class="text-[10px] text-orange-600 hover:text-orange-800">Logout</button>
                            </form>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="px-3 py-1 rounded bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100">Login</a>
                    @endguest
                </div>
            </div>
        </header>
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 py-6 pb-10">

        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
        </main>
    </div>
</body>
</html>
