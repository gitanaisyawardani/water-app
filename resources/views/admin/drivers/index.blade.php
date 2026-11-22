<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            🚛 Master Data Driver & Ketersediaan (PBI-04)
        </h2>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, editMode: false, form: { id: '', user_id: '', name: '', phone: '', license_number: '', status: 'ready' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Action Bar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.drivers.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, telp, atau SIM..." class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready (Tersedia)</option>
                        <option value="on_duty" {{ request('status') == 'on_duty' ? 'selected' : '' }}>On Duty (Bertugas)</option>
                        <option value="off" {{ request('status') == 'off' ? 'selected' : '' }}>Libur / Off</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.drivers.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                    @endif
                </form>

                <button @click="editMode = false; form = { id: '', user_id: '', name: '', phone: '', license_number: '', status: 'ready' }; showModal = true"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm flex items-center justify-center gap-2">
                    <span>+ Tambah Pengemudi (Driver)</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Nama Sopir</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">No. WhatsApp / HP</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Nomor Lisensi SIM</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Status Ketersediaan</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($drivers as $driver)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $driver->name }}</div>
                                @if($driver->user)
                                    <div class="text-xs text-blue-600">Akun terhubung: {{ $driver->user->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-mono">
                                {{ $driver->phone }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-mono">
                                {{ $driver->license_number }}
                            </td>
                            <td class="px-6 py-4">
                                @if($driver->status === 'ready')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Ready (Tersedia)
                                    </span>
                                @elseif($driver->status === 'on_duty')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> On Duty (Mengantar)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <span class="h-2 w-2 rounded-full bg-gray-400"></span> Libur (Off)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <button @click="editMode = true; form = { id: '{{ $driver->id }}', user_id: '{{ $driver->user_id }}', name: '{{ addslashes($driver->name) }}', phone: '{{ $driver->phone }}', license_number: '{{ $driver->license_number }}', status: '{{ $driver->status }}' }; showModal = true"
                                    class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>

                                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="inline" onsubmit="return confirm('Hapus driver ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data driver ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $drivers->links() }}
                </div>
            </div>
        </div>

        <!-- Modal Tambah / Edit Driver -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                <div class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-10 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editMode ? 'Edit Data Driver' : 'Tambah Driver Baru'"></h3>

                    <form :action="editMode ? '{{ url('admin/drivers') }}/' + form.id : '{{ route('admin.drivers.store') }}'" method="POST">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Lengkap Driver</label>
                                <input type="text" name="name" x-model="form.name" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">No. WhatsApp / HP</label>
                                    <input type="text" name="phone" x-model="form.phone" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Nomor SIM (B1/B2)</label>
                                    <input type="text" name="license_number" x-model="form.license_number" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Status Ketersediaan</label>
                                    <select name="status" x-model="form.status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="ready">Ready (Tersedia)</option>
                                        <option value="on_duty">On Duty (Bertugas)</option>
                                        <option value="off">Libur (Off)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Tautkan Akun User (Opsional)</label>
                                    <select name="user_id" x-model="form.user_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Belum Ditautkan --</option>
                                        @foreach($driverUsers as $du)
                                            <option value="{{ $du->id }}">{{ $du->name }} ({{ $du->email }})</option>
                                        @endforeach
                                    </select>
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
</x-app-layout>
