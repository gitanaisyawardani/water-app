<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            ⚙️ Konfigurasi Harga Dasar Air & Tarif KM (PBI-06)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Depo Pusat -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-base font-bold text-gray-900 mb-1">📍 Titik Pusat Depo Air</h3>
                        <p class="text-xs text-gray-500 mb-4">Titik acuan penghitungan jarak kilometer pengiriman ke lokasi pelanggan.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Depo</label>
                                <input type="text" name="depo_name" value="{{ $settings['depo_name']->value ?? 'Depo Pusat' }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Alamat Depo</label>
                                <input type="text" name="depo_address" value="{{ $settings['depo_address']->value ?? '' }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Koordinat Latitude Depo</label>
                                <input type="text" name="depo_latitude" value="{{ $settings['depo_latitude']->value ?? '-6.304033' }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Koordinat Longitude Depo</label>
                                <input type="text" name="depo_longitude" value="{{ $settings['depo_longitude']->value ?? '107.157567' }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Tarif Jarak KM -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-base font-bold text-gray-900 mb-1">🛣️ Tarif Ongkos Kirim per KM</h3>
                        <p class="text-xs text-gray-500 mb-4">Biaya tambahan yang dikalikan dengan jarak tempuh aktual (KM).</p>

                        <div class="max-w-xs">
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Tarif per KM (Rupiah)</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" name="rate_per_km" value="{{ $settings['rate_per_km']->value ?? 15000 }}" required class="block w-full rounded-lg border-gray-300 pl-10 pr-12 text-sm font-semibold focus:ring-blue-500 focus:border-blue-500 font-mono">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-xs">/ KM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Harga Dasar Air per Tangki -->
                    <div class="pb-2">
                        <h3 class="text-base font-bold text-gray-900 mb-1">💧 Harga Dasar Air Tangki (Berdasarkan Volume)</h3>
                        <p class="text-xs text-gray-500 mb-4">Harga dasar air sebelum ditambah ongkos jarak.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">7.000 Liter (7k)</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-400 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="price_7000" value="{{ $settings['price_7000']->value ?? 250000 }}" required class="block w-full rounded-lg border-gray-300 pl-8 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">8.000 Liter (8k)</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-400 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="price_8000" value="{{ $settings['price_8000']->value ?? 300000 }}" required class="block w-full rounded-lg border-gray-300 pl-8 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">16.000 Liter (16k)</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-400 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="price_16000" value="{{ $settings['price_16000']->value ?? 550000 }}" required class="block w-full rounded-lg border-gray-300 pl-8 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">20.000 Liter (20k)</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-400 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="price_20000" value="{{ $settings['price_20000']->value ?? 700000 }}" required class="block w-full rounded-lg border-gray-300 pl-8 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm flex items-center gap-2">
                            <span>Simpan Perubahan Tarif</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
