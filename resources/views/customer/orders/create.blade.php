<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            💧 Pemesanan Air Tangki Mandiri (PBI-07 & PBI-08)
        </h2>
    </x-slot>

    <div class="py-8" x-data="orderCalculator()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('customer.orders.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Kolom Kiri: Form Isian & Peta (2 Kolom) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Step 1: Pilihan Kapasitas Tangki -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">1</span>
                                Pilih Kapasitas Air Tangki
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">Pilih volume tangki sesuai kebutuhan operasional usaha atau proyek Anda.</p>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <template x-for="cap in [7000, 8000, 16000, 20000]" :key="cap">
                                    <label :class="tankCapacity == cap ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500' : 'border-gray-200 hover:border-gray-300'"
                                        class="cursor-pointer border rounded-xl p-4 flex flex-col items-center text-center transition">
                                        <input type="radio" name="tank_capacity" :value="cap" x-model="tankCapacity" @change="recalculate()" class="sr-only">
                                        <span class="text-2xl mb-1">🚛</span>
                                        <span class="font-bold text-gray-900" x-text="(cap / 1000) + 'k Liter'"></span>
                                        <span class="text-xs text-gray-500 mt-1" x-text="'Rp ' + formatRupiah(prices[cap])"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Step 2: Jadwal Pengiriman -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">2</span>
                                Jadwal Pengiriman
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">Tentukan tanggal dan perkiraan jam air tangki tiba di lokasi.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Antar</label>
                                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Perkiraan Waktu (Jam)</label>
                                    <input type="time" name="delivery_time" value="09:00" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Lokasi & Peta Titik Pengantaran -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">3</span>
                                Lokasi Pengantaran & Titik Peta (Leaflet.js)
                            </h3>
                            <p class="text-xs text-gray-500 mb-3">Geser pin biru pada peta untuk menentukan titik drop air dan menghitung jarak real-time dari Depo.</p>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Alamat Lengkap</label>
                                    <textarea name="delivery_address" rows="2" required placeholder="Nama jalan, nomor, patokan lokasi..." class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">{{ $customer->address ?? '' }}</textarea>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-semibold text-gray-700 uppercase">Peta Interaktif</span>
                                        <span class="text-xs text-blue-600 font-mono" x-text="'Jarak: ' + distanceKm.toFixed(2) + ' KM dari Depo'"></span>
                                    </div>
                                    <div id="orderMap" class="w-full h-72 rounded-xl border border-gray-300 z-0 shadow-inner"></div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500">Latitude Drop Point</label>
                                        <input type="text" name="delivery_latitude" x-model="customerLat" readonly required class="mt-1 block w-full rounded-lg bg-gray-50 border-gray-300 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500">Longitude Drop Point</label>
                                        <input type="text" name="delivery_longitude" x-model="customerLng" readonly required class="mt-1 block w-full rounded-lg bg-gray-50 border-gray-300 text-xs font-mono">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Catatan Khusus Pengemudi (Opsional)</label>
                                    <input type="text" name="notes" placeholder="Contoh: Sambungan selang 2 inch, masuk dari pintu gerbang barat" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Kanan: Rincian Kalkulasi Biaya Otomatis (PBI-08) -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                                <span>🧾</span> Rincian Estimasi Biaya
                            </h3>

                            <div class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between text-gray-600">
                                    <span>Volume Air Tangki:</span>
                                    <span class="font-semibold text-gray-900" x-text="number_format(tankCapacity) + ' Liter'"></span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Harga Dasar Air:</span>
                                    <span class="font-semibold text-gray-900" x-text="'Rp ' + formatRupiah(waterPrice)"></span>
                                </div>

                                <div class="border-t border-gray-100 pt-3 flex justify-between text-gray-600">
                                    <span>Estimasi Jarak Pengiriman:</span>
                                    <span class="font-semibold text-blue-600 font-mono" x-text="distanceKm.toFixed(2) + ' KM'"></span>
                                </div>
                                <div class="flex justify-between text-gray-500 text-xs">
                                    <span>Tarif Ongkir (/ KM):</span>
                                    <span x-text="'Rp ' + formatRupiah(ratePerKm)"></span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Biaya Ongkos Kirim:</span>
                                    <span class="font-semibold text-gray-900" x-text="'Rp ' + formatRupiah(deliveryFee)"></span>
                                </div>

                                <div class="border-t-2 border-dashed border-gray-200 pt-4 mt-2">
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-base font-bold text-gray-900">Total Pembayaran:</span>
                                        <span class="text-xl font-extrabold text-blue-600 font-mono" x-text="'Rp ' + formatRupiah(totalPrice)"></span>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">*Formula: (Volume × Harga Air) + (Jarak KM × Tarif KM)</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                                    <span>Konfirmasi & Buat Pesanan</span>
                                    <span>➔</span>
                                </button>
                                <p class="text-center text-xs text-gray-400 mt-2">Pesanan akan divalidasi oleh admin/owner.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function orderCalculator() {
            return {
                tankCapacity: 8000,
                depoLat: {{ $depoLat }},
                depoLng: {{ $depoLng }},
                customerLat: {{ $customer->latitude ?? $depoLat + 0.015 }},
                customerLng: {{ $customer->longitude ?? $depoLng + 0.015 }},
                ratePerKm: {{ $ratePerKm }},
                prices: @json($prices),
                distanceKm: 0,
                waterPrice: 0,
                deliveryFee: 0,
                totalPrice: 0,
                map: null,
                customerMarker: null,

                init() {
                    this.recalculate();
                    this.$nextTick(() => { this.initMap(); });
                },

                recalculate() {
                    // Haversine distance formula
                    this.distanceKm = this.calculateDistance(this.depoLat, this.depoLng, this.customerLat, this.customerLng);
                    this.waterPrice = this.prices[this.tankCapacity] || 0;
                    this.deliveryFee = Math.round(this.distanceKm * this.ratePerKm);
                    this.totalPrice = this.waterPrice + this.deliveryFee;
                },

                calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371; // Radius bumi dalam KM
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    const d = R * c;
                    return Math.max(Math.round(d * 100) / 100, 1.0);
                },

                initMap() {
                    this.map = L.map('orderMap').setView([this.customerLat, this.customerLng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // Marker Depo (Merah / Pusat)
                    const depoIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    L.marker([this.depoLat, this.depoLng], { icon: depoIcon })
                        .addTo(this.map)
                        .bindPopup("<b>{{ $depoName }}</b><br>Titik Awal Pengiriman");

                    // Marker Pengantaran Customer (Bisa di-drag)
                    this.customerMarker = L.marker([this.customerLat, this.customerLng], { draggable: true })
                        .addTo(this.map)
                        .bindPopup("Lokasi Pengantaran Air Anda (Bisa digeser)");

                    this.customerMarker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.customerLat = Number(pos.lat.toFixed(7));
                        this.customerLng = Number(pos.lng.toFixed(7));
                        this.recalculate();
                    });

                    this.map.on('click', (e) => {
                        this.customerMarker.setLatLng(e.latlng);
                        this.customerLat = Number(e.latlng.lat.toFixed(7));
                        this.customerLng = Number(e.latlng.lng.toFixed(7));
                        this.recalculate();
                    });
                },

                formatRupiah(val) {
                    return Number(val || 0).toLocaleString('id-ID');
                },

                number_format(val) {
                    return Number(val || 0).toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>
