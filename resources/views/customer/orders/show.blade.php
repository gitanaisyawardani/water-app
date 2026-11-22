<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Detail Pesanan #{{ $order->order_number }}
            </h2>
            <a href="{{ route('customer.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                ← Kembali ke Daftar Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Status Banner -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Status Pesanan Saat Ini</span>
                    <div class="text-lg font-extrabold text-gray-900 mt-0.5">
                        @if($order->status === 'pending')
                            ⏳ Menunggu Validasi & Penugasan Armada
                        @elseif($order->status === 'approved')
                            ✅ Pesanan Disetujui (Sedang Disiapkan)
                        @elseif($order->status === 'delivering')
                            🚚 Armada Sedang Dalam Perjalanan ke Lokasi Anda
                        @elseif($order->status === 'completed')
                            🎉 Pengantaran Selesai & Diterima
                        @else
                            ❌ Pesanan Dibatalkan / Ditolak
                        @endif
                    </div>
                </div>

                <div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase
                        @if($order->status === 'pending') bg-amber-100 text-amber-800
                        @elseif($order->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($order->status === 'delivering') bg-purple-100 text-purple-800
                        @elseif($order->status === 'completed') bg-emerald-100 text-emerald-800
                        @else bg-rose-100 text-rose-800 @endif">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <!-- Detail Pesanan & Lokasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Info Pengiriman -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2">📍 Info Pengantaran</h3>

                    <div>
                        <span class="text-xs text-gray-400 uppercase font-semibold">Alamat Pengiriman</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $order->delivery_address }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Jadwal Tanggal</span>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Waktu Tiba</span>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">Jam {{ substr($order->delivery_time, 0, 5) }} WIB</p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 uppercase font-semibold">Koordinat Titik Drop</span>
                        <p class="text-xs font-mono text-blue-600 mt-0.5">{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}</p>
                    </div>

                    @if($order->notes)
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Catatan Khusus</span>
                            <p class="text-sm text-gray-600 italic mt-0.5">"{{ $order->notes }}"</p>
                        </div>
                    @endif
                </div>

                <!-- Rincian Biaya -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2">🧾 Rincian Pembayaran</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Volume Air Tangki:</span>
                            <span class="font-bold text-gray-900">{{ number_format($order->tank_capacity, 0, ',', '.') }} Liter</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga Dasar Air:</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($order->water_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jarak Tempuh:</span>
                            <span class="font-bold text-blue-600">{{ $order->distance_km }} KM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim Jarak:</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 pt-3 flex justify-between items-baseline">
                            <span class="text-base font-extrabold text-gray-900">Total Tagihan:</span>
                            <span class="text-xl font-extrabold text-blue-600 font-mono">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
