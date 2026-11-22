<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            🚚 Master Data Armada Truk Tangki (PBI-05)
        </h2>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, editMode: false, form: { id: '', plate_number: '', brand: '', capacity: 8000, status: 'ready', notes: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Action Bar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.trucks.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari plat atau merek..." class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <select name="capacity" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kapasitas</option>
                        <option value="7000" {{ request('capacity') == '7000' ? 'selected' : '' }}>7.000 Liter (7k)</option>
                        <option value="8000" {{ request('capacity') == '8000' ? 'selected' : '' }}>8.000 Liter (8k)</option>
                        <option value="16000" {{ request('capacity') == '16000' ? 'selected' : '' }}>16.000 Liter (16k)</option>
                        <option value="20000" {{ request('capacity') == '20000' ? 'selected' : '' }}>20.000 Liter (20k)</option>
                    </select>
                    <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready (Siap Operasi)</option>
                        <option value="on_duty" {{ request('status') == 'on_duty' ? 'selected' : '' }}>On Duty (Sedang Jalan)</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance (Servis)</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">Filter</button>
                    @if(request('search') || request('capacity') || request('status'))
                        <a href="{{ route('admin.trucks.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                    @endif
                </form>

                <button @click="editMode = false; form = { id: '', plate_number: '', brand: '', capacity: 8000, status: 'ready', notes: '' }; showModal = true"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm flex items-center justify-center gap-2">
                    <span>+ Tambah Armada Truk</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Plat Nomor</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Tipe / Merek</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Kapasitas Tangki</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Status Armada</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Catatan</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($trucks as $truck)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2.5 py-1 rounded border border-gray-300">
                                    {{ $truck->plate_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $truck->brand ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    💧 {{ number_format($truck->capacity, 0, ',', '.') }} Liter
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($truck->status === 'ready')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Ready
                                    </span>
                                @elseif($truck->status === 'on_duty')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> On Duty
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        <span class="h-2 w-2 rounded-full bg-rose-500"></span> Maintenance
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                                {{ $truck->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <button @click="editMode = true; form = { id: '{{ $truck->id }}', plate_number: '{{ $truck->plate_number }}', brand: '{{ addslashes($truck->brand) }}', capacity: {{ $truck->capacity }}, status: '{{ $truck->status }}', notes: '{{ addslashes($truck->notes) }}' }; showModal = true"
                                    class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>

                                <form action="{{ route('admin.trucks.destroy', $truck) }}" method="POST" class="inline" onsubmit="return confirm('Hapus armada ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data armada truk ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $trucks->links() }}
                </div>
            </div>
        </div>

        <!-- Modal Tambah / Edit Truk -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                <div class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-10 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editMode ? 'Edit Data Armada Truk' : 'Tambah Armada Truk Baru'"></h3>

                    <form :action="editMode ? '{{ url('admin/trucks') }}/' + form.id : '{{ route('admin.trucks.store') }}'" method="POST">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Plat Nomor Polisi</label>
                                    <input type="text" name="plate_number" x-model="form.plate_number" placeholder="Contoh: B 9105 TKE" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 uppercase font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Merek / Varian</label>
                                    <input type="text" name="brand" x-model="form.brand" placeholder="Hino Dutro / Canter" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Kapasitas Tangki</label>
                                    <select name="capacity" x-model="form.capacity" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 font-semibold">
                                        <option value="7000">7.000 Liter (7k)</option>
                                        <option value="8000">8.000 Liter (8k)</option>
                                        <option value="16000">16.000 Liter (16k)</option>
                                        <option value="20000">20.000 Liter (20k)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Status Kelayakan</label>
                                    <select name="status" x-model="form.status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="ready">Ready (Siap)</option>
                                        <option value="on_duty">On Duty (Operasi)</option>
                                        <option value="maintenance">Maintenance (Servis)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Catatan Kondisi / Uji KIR</label>
                                <textarea name="notes" x-model="form.notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Kondisi tangki, ban, masa berlaku KIR..."></textarea>
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
</x-app-layout>
