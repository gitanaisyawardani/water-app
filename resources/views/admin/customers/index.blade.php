<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            👥 Master Data Pelanggan & Kategori Bisnis (PBI-02 & PBI-03)
        </h2>
    </x-slot>

    <div class="py-8" x-data="customerManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Action Bar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, WA, atau alamat..." class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <select name="category" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        <option value="Hotel" {{ request('category') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                        <option value="Pabrik" {{ request('category') == 'Pabrik' ? 'selected' : '' }}>Pabrik</option>
                        <option value="Proyek" {{ request('category') == 'Proyek' ? 'selected' : '' }}>Proyek</option>
                        <option value="Restoran" {{ request('category') == 'Restoran' ? 'selected' : '' }}>Restoran</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">Filter</button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                    @endif
                </form>

                <button @click="openCreateModal()"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm flex items-center justify-center gap-2">
                    <span>+ Tambah Pelanggan Baru</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Nama Pelanggan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Kategori Bisnis</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">No. WhatsApp</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Alamat & Koordinat Peta</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $customer->name }}</div>
                                @if($customer->user)
                                    <div class="text-xs text-gray-400">Akun: {{ $customer->user->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full
                                    @if($customer->business_category === 'Hotel') bg-indigo-100 text-indigo-800
                                    @elseif($customer->business_category === 'Pabrik') bg-amber-100 text-amber-800
                                    @elseif($customer->business_category === 'Proyek') bg-rose-100 text-rose-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    {{ $customer->business_category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-800">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->whatsapp_number) }}" target="_blank" class="text-emerald-600 hover:underline flex items-center gap-1">
                                    💬 {{ $customer->whatsapp_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>{{ $customer->address }}</div>
                                @if($customer->latitude && $customer->longitude)
                                    <div class="text-xs font-mono text-blue-600 flex items-center gap-1 mt-0.5">
                                        📍 <span>{{ $customer->latitude }}, {{ $customer->longitude }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <button @click="openEditModal({{ json_encode($customer) }})"
                                    class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>

                                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data pelanggan ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>

        <!-- Modal Form Input dengan Integrasi Leaflet.js -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                <div class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full z-10 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editMode ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru'"></h3>

                    <form :action="editMode ? '{{ url('admin/customers') }}/' + form.id : '{{ route('admin.customers.store') }}'" method="POST">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Pelanggan / Usaha</label>
                                    <input type="text" name="name" x-model="form.name" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Kategori Bisnis (PBI-03)</label>
                                    <select name="business_category" x-model="form.business_category" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 font-semibold">
                                        <option value="Hotel">Hotel</option>
                                        <option value="Pabrik">Pabrik</option>
                                        <option value="Proyek">Proyek</option>
                                        <option value="Restoran">Restoran</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Nomor WhatsApp Aktif</label>
                                <input type="text" name="whatsapp_number" x-model="form.whatsapp_number" placeholder="Contoh: 081234567890" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Alamat Lengkap</label>
                                <textarea name="address" x-model="form.address" rows="2" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <!-- Peta Leaflet.js untuk Titik Koordinat Peta -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">
                                        📍 Titik Koordinat Pengantaran (Klik peta untuk menandai)
                                    </label>
                                    <span class="text-xs text-blue-600 font-mono" x-text="form.latitude && form.longitude ? form.latitude + ', ' + form.longitude : 'Belum ditandai'"></span>
                                </div>
                                <div id="customerMap" class="w-full h-56 rounded-lg border border-gray-300 z-0"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500">Latitude</label>
                                    <input type="text" name="latitude" x-model="form.latitude" readonly required class="mt-1 block w-full rounded-lg bg-gray-50 border-gray-300 text-xs font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500">Longitude</label>
                                    <input type="text" name="longitude" x-model="form.longitude" readonly required class="mt-1 block w-full rounded-lg bg-gray-50 border-gray-300 text-xs font-mono">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function customerManager() {
            return {
                showModal: false,
                editMode: false,
                map: null,
                marker: null,
                depoLat: {{ $depoLat }},
                depoLng: {{ $depoLng }},
                form: {
                    id: '',
                    name: '',
                    whatsapp_number: '',
                    business_category: 'Hotel',
                    address: '',
                    latitude: {{ $depoLat }},
                    longitude: {{ $depoLng }}
                },

                openCreateModal() {
                    this.editMode = false;
                    this.form = {
                        id: '',
                        name: '',
                        whatsapp_number: '',
                        business_category: 'Hotel',
                        address: '',
                        latitude: this.depoLat,
                        longitude: this.depoLng
                    };
                    this.showModal = true;
                    this.$nextTick(() => { this.initMap(); });
                },

                openEditModal(customer) {
                    this.editMode = true;
                    this.form = {
                        id: customer.id,
                        name: customer.name,
                        whatsapp_number: customer.whatsapp_number,
                        business_category: customer.business_category,
                        address: customer.address,
                        latitude: customer.latitude || this.depoLat,
                        longitude: customer.longitude || this.depoLng
                    };
                    this.showModal = true;
                    this.$nextTick(() => { this.initMap(); });
                },

                initMap() {
                    const lat = parseFloat(this.form.latitude) || this.depoLat;
                    const lng = parseFloat(this.form.longitude) || this.depoLng;

                    if (!this.map) {
                        this.map = L.map('customerMap').setView([lat, lng], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(this.map);

                        this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.form.latitude = pos.lat.toFixed(7);
                            this.form.longitude = pos.lng.toFixed(7);
                        });

                        this.map.on('click', (e) => {
                            this.marker.setLatLng(e.latlng);
                            this.form.latitude = e.latlng.lat.toFixed(7);
                            this.form.longitude = e.latlng.lng.toFixed(7);
                        });
                    } else {
                        this.map.invalidateSize();
                        this.map.setView([lat, lng], 13);
                        this.marker.setLatLng([lat, lng]);
                    }
                }
            };
        }
    </script>
</x-app-layout>
