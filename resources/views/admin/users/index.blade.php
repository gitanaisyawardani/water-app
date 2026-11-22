<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            🔐 Manajemen Pengguna & Hak Akses (PBI-01)
        </h2>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, editMode: false, form: { id: '', name: '', email: '', role: 'customer', status: 'active', phone: '', password: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Action Bar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <select name="role" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="driver" {{ request('role') == 'driver' ? 'selected' : '' }}>Driver</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">Filter</button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                    @endif
                </form>

                <button @click="editMode = false; form = { id: '', name: '', email: '', role: 'customer', status: 'active', phone: '', password: '' }; showModal = true"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm flex items-center justify-center gap-2">
                    <span>+ Tambah Pengguna</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Nama & Email</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">No. Telepon</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Role / Akses</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase
                                    @if($user->role === 'admin') bg-purple-100 text-purple-800
                                    @elseif($user->role === 'owner') bg-amber-100 text-amber-800
                                    @elseif($user->role === 'driver') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <button @click="editMode = true; form = { id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}', email: '{{ $user->email }}', role: '{{ $user->role }}', status: '{{ $user->status }}', phone: '{{ $user->phone }}', password: '' }; showModal = true"
                                    class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>

                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data pengguna ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Modal Tambah / Edit User -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                <div class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-10 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>

                    <form :action="editMode ? '{{ url('admin/users') }}/' + form.id : '{{ route('admin.users.store') }}'" method="POST">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Lengkap</label>
                                <input type="text" name="name" x-model="form.name" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Email</label>
                                <input type="email" name="email" x-model="form.email" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Role / Hak Akses</label>
                                    <select name="role" x-model="form.role" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="customer">Customer</option>
                                        <option value="driver">Driver</option>
                                        <option value="owner">Owner</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase">Status Akun</label>
                                    <select name="status" x-model="form.status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">No. WhatsApp / Telepon</label>
                                <input type="text" name="phone" x-model="form.phone" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase" x-text="editMode ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password'"></label>
                                <input type="password" name="password" :required="!editMode" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
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
