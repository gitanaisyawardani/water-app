<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TruckController extends Controller
{
    public function index(Request $request)
    {
        $query = Truck::query();

        if ($request->filled('capacity')) {
            $query->where('capacity', $request->capacity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('plate_number', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }

        $trucks = $query->latest()->paginate(10)->withQueryString();
        return view('admin.trucks.index', compact('trucks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:trucks,plate_number',
            'brand'        => 'nullable|string|max:100',
            'capacity'     => ['required', 'integer', Rule::in([7000, 8000, 16000, 20000])],
            'status'       => ['required', Rule::in(['ready', 'on_duty', 'maintenance'])],
            'notes'        => 'nullable|string',
        ]);

        Truck::create($validated);

        return redirect()->route('admin.trucks.index')->with('success', 'Armada truk tangki berhasil ditambahkan!');
    }

    public function update(Request $request, Truck $truck)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('trucks')->ignore($truck->id)],
            'brand'        => 'nullable|string|max:100',
            'capacity'     => ['required', 'integer', Rule::in([7000, 8000, 16000, 20000])],
            'status'       => ['required', Rule::in(['ready', 'on_duty', 'maintenance'])],
            'notes'        => 'nullable|string',
        ]);

        $truck->update($validated);

        return redirect()->route('admin.trucks.index')->with('success', 'Data armada truk tangki berhasil diperbarui!');
    }

    public function destroy(Truck $truck)
    {
        $truck->delete();
        return redirect()->route('admin.trucks.index')->with('success', 'Armada berhasil dihapus!');
    }
}
