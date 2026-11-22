<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                📋 Riwayat Pesanan Air Tangki Anda
            </h2>
            <a href="{{ route('customer.orders.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm">
                + Pesan Air Lagi
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">No. Pesanan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Volume Tangki</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Jadwal Pengiriman</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Total Biaya</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-blue-600 text-sm">{{ $order->order_number }}</span>
                                <div class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    💧 {{ number_format($order->tank_capacity, 0, ',', '.') }} Liter
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                <div>📅 {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">⏰ Jam {{ substr($order->delivery_time, 0, 5) }} WIB</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                <div class="text-[11px] text-gray-400 font-normal">({{ $order->distance_km }} KM)</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($order->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> Menunggu Validasi
                                    </span>
                                @elseif($order->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <span class="h-2 w-2 rounded-full bg-blue-500"></span> Disetujui (Diproses)
                                    </span>
                                @elseif($order->status === 'delivering')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        <span class="h-2 w-2 rounded-full bg-purple-500"></span> Sedang Dikirim
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        <span class="h-2 w-2 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('customer.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                                    Lihat Rincian & Invoice ➔
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <div class="text-3xl mb-2">🚚</div>
                                <div class="font-semibold text-gray-700">Belum ada riwayat pesanan air tangki.</div>
                                <div class="text-xs text-gray-400 mt-1">Buat pesanan pertama Anda untuk mulai pengantaran.</div>
                                <a href="{{ route('customer.orders.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold">
                                    + Pesan Sekarang
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
