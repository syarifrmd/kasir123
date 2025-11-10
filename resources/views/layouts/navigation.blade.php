<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-14 flex items-center justify-between">
            <!-- Left: Brand + Links -->
            <div class="flex items-center gap-4">
                <a href="{{ route('pos.index') }}" class="text-base sm:text-lg font-semibold text-rose-600 hover:text-rose-700">
                    Kasir
                </a>
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('barang.index') }}" class="text-sm px-2 py-1 rounded hover:bg-gray-100 {{ request()->is('barang*') ? 'text-rose-600 font-medium' : 'text-gray-700' }}">Barang</a>
                    <a href="{{ route('transaksi.index') }}" class="text-sm px-2 py-1 rounded hover:bg-gray-100 {{ request()->is('transaksi*') ? 'text-rose-600 font-medium' : 'text-gray-700' }}">Transaksi</a>
                </div>
            </div>

            <!-- Right: Admin + Auth -->
            <div class="hidden sm:flex items-center gap-3">
                <a href="/admin" class="text-sm px-3 py-1.5 rounded border border-gray-200 text-gray-700 hover:bg-gray-50">Admin</a>
                @auth
                    <div class="relative" x-data="{ m:false }">
                        <button @click="m=!m" class="text-sm px-3 py-1.5 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                            {{ Auth::user()->name }}
                        </button>
                        <div x-show="m" @click.outside="m=false" x-cloak class="absolute right-0 mt-2 w-40 bg-white border rounded shadow">
                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm hover:bg-gray-50">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left block px-3 py-2 text-sm hover:bg-gray-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm px-3 py-1.5 rounded bg-rose-600 text-white hover:bg-rose-700">Login</a>
                @endauth
            </div>

            <!-- Mobile hamburger -->
            <button @click="open=!open" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:bg-gray-100">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" x-show="open" x-cloak>
        <div class="px-4 pb-3 space-y-1">
            <a href="{{ route('barang.index') }}" class="block px-3 py-2 rounded {{ request()->is('barang*') ? 'bg-rose-50 text-rose-700' : 'hover:bg-gray-50' }}">Barang</a>
            <a href="{{ route('transaksi.index') }}" class="block px-3 py-2 rounded {{ request()->is('transaksi*') ? 'bg-rose-50 text-rose-700' : 'hover:bg-gray-50' }}">Transaksi</a>
            <a href="/admin" class="block px-3 py-2 rounded hover:bg-gray-50">Admin</a>
            <div class="border-t pt-2 mt-2">
                @auth
                    <div class="px-3 py-1 text-sm text-gray-700">{{ Auth::user()->name }}</div>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded hover:bg-gray-50">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                        @csrf
                        <button class="w-full text-left rounded hover:bg-gray-50">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded bg-rose-600 text-white hover:bg-rose-700">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
