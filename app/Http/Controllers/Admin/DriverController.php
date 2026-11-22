<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('license_number', 'like', '%' . $request->search . '%');
            });
        }

        $drivers = $query->latest()->paginate(10)->withQueryString();
        $driverUsers = User::where('role', 'driver')->get();

        return view('admin.drivers.index', compact('drivers', 'driverUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'nullable|exists:users,id',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:25',
            'license_number' => 'required|string|max:50',
            'status'         => ['required', Rule::in(['ready', 'off', 'on_duty'])],
        ]);

        Driver::create($validated);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver baru berhasil ditambahkan!');
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'user_id'        => 'nullable|exists:users,id',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:25',
            'license_number' => 'required|string|max:50',
            'status'         => ['required', Rule::in(['ready', 'off', 'on_duty'])],
        ]);

        $driver->update($validated);

        return redirect()->route('admin.drivers.index')->with('success', 'Data driver berhasil diperbarui!');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->route('admin.drivers.index')->with('success', 'Driver berhasil dihapus!');
    }
}
