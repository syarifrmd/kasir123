@extends('layouts.kasir')
@section('title', 'Dashboard')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Ringkasan & Analisis Bisnis</p>
        </div>
        
        <!-- Date Filter -->
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded-lg px-3 py-2 text-sm">
            <span class="text-gray-500">—</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded-lg px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium hover:bg-rose-700">
                Filter
            </button>
            <a href="{{ route('dashboard.index') }}" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">Reset</a>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium opacity-90">Total Pendapatan</div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="text-3xl font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-xs opacity-75 mt-1">{{ $totalTransactions }} transaksi</div>
        </div>

        <!-- Total Profit -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium opacity-90">Total Keuntungan</div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div class="text-3xl font-bold">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
            <div class="text-xs opacity-75 mt-1">Margin: {{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : 0 }}%</div>
        </div>

        <!-- Stock Value -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium opacity-90">Nilai Stok</div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div class="text-3xl font-bold">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</div>
            <div class="text-xs opacity-75 mt-1">Total inventori</div>
        </div>

        <!-- Stock Alerts -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium opacity-90">Peringatan Stok</div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="text-3xl font-bold">{{ $lowStock + $outOfStock }}</div>
            <div class="text-xs opacity-75 mt-1">{{ $lowStock }} rendah, {{ $outOfStock }} habis</div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profit Trend Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-bold text-lg mb-4 text-gray-900">Tren Keuntungan Harian</h2>
            <div style="position: relative; height: 300px;">
                <canvas id="profitTrendChart"></canvas>
            </div>
        </div>

        <!-- Stock by Category Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-bold text-lg mb-4 text-gray-900">Stok Berdasarkan Kategori</h2>
            <div style="position: relative; height: 300px;">
                <canvas id="stockCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue by Category Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-bold text-lg mb-4 text-gray-900">Pendapatan per Kategori</h2>
            <div style="position: relative; height: 300px;">
                <canvas id="revenueCategoryChart"></canvas>
            </div>
        </div>

        <!-- Top Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-lg text-gray-900">Top 10 Produk Terlaris</h2>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">Produk</th>
                            <th class="px-4 py-2 text-right font-semibold text-gray-700">Qty</th>
                            <th class="px-4 py-2 text-right font-semibold text-gray-700">Revenue</th>
                            <th class="px-4 py-2 text-right font-semibold text-gray-700">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topProducts as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="font-medium">{{ $product->merk }} {{ $product->jenis }}</div>
                                    <div class="text-xs text-gray-500">{{ \App\Models\Barang::KATEGORI[$product->kategori] ?? $product->kategori }}</div>
                                </td>
                                <td class="px-4 py-2 text-right">{{ $product->total_qty }}</td>
                                <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-green-600 font-medium">Rp {{ number_format($product->total_profit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="font-bold text-lg text-gray-900">Pergerakan Stok Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Waktu</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Barang</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Tipe</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Vendor</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Qty</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentMovements as $mv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-600">{{ $mv->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $mv->barang->merk }} {{ $mv->barang->jenis }}</div>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $mv->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ strtoupper($mv->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $mv->vendor->nama ?? '-' }}</td>
                            <td class="px-4 py-2 text-right font-medium">{{ $mv->qty }}</td>
                            <td class="px-4 py-2 text-right text-gray-600">{{ $mv->before_stock }} → {{ $mv->after_stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Profit Trend Chart
    const profitTrendCtx = document.getElementById('profitTrendChart');
    if (profitTrendCtx && !profitTrendCtx.chartInstance) {
        profitTrendCtx.chartInstance = new Chart(profitTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($profitTrend->pluck('date')),
                datasets: [{
                    label: 'Keuntungan',
                    data: @json($profitTrend->pluck('total_profit')),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Pendapatan',
                    data: @json($profitTrend->pluck('total_revenue')),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                        }
                    }
                }
            }
        });
    }

    // Stock by Category Chart
    const stockCategoryCtx = document.getElementById('stockCategoryChart');
    if (stockCategoryCtx && !stockCategoryCtx.chartInstance) {
        stockCategoryCtx.chartInstance = new Chart(stockCategoryCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($stockByCategory->pluck('kategori')->map(function($k) { return \App\Models\Barang::KATEGORI[$k] ?? $k; })),
                datasets: [{
                    label: 'Total Stok (unit)',
                    data: @json($stockByCategory->pluck('total_stok')),
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' unit';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Revenue by Category Chart (Pie)
    const revenueCategoryCtx = document.getElementById('revenueCategoryChart');
    if (revenueCategoryCtx && !revenueCategoryCtx.chartInstance) {
        revenueCategoryCtx.chartInstance = new Chart(revenueCategoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json($revenueByCategory->pluck('kategori')->map(function($k) { return \App\Models\Barang::KATEGORI[$k] ?? $k; })),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($revenueByCategory->pluck('total_revenue')),
                    backgroundColor: [
                        'rgb(239, 68, 68)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(249, 115, 22)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
