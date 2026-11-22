<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('user');

        if ($request->filled('category')) {
            $query->where('business_category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('whatsapp_number', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();
        $depoLat = Setting::get('depo_latitude', -6.304033);
        $depoLng = Setting::get('depo_longitude', 107.157567);

        return view('admin.customers.index', compact('customers', 'depoLat', 'depoLng'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'whatsapp_number'   => 'required|string|max:25',
            'business_category' => ['required', Rule::in(['Hotel', 'Pabrik', 'Proyek', 'Restoran'])],
            'address'           => 'required|string',
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil ditambahkan!');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'whatsapp_number'   => 'required|string|max:25',
            'business_category' => ['required', Rule::in(['Hotel', 'Pabrik', 'Proyek', 'Restoran'])],
            'address'           => 'required|string',
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil dihapus!');
    }
}
