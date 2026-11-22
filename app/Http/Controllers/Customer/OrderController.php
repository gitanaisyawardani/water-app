<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function create()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $depoLat = (float) Setting::get('depo_latitude', -6.304033);
        $depoLng = (float) Setting::get('depo_longitude', 107.157567);
        $depoName = Setting::get('depo_name', 'Depo Utama Tirta Tangki');

        $ratePerKm = (float) Setting::get('rate_per_km', 15000);
        $prices = [
            7000  => (float) Setting::get('price_7000', 250000),
            8000  => (float) Setting::get('price_8000', 300000),
            16000 => (float) Setting::get('price_16000', 550000),
            20000 => (float) Setting::get('price_20000', 700000),
        ];

        return view('customer.orders.create', compact('customer', 'depoLat', 'depoLng', 'depoName', 'ratePerKm', 'prices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tank_capacity'      => ['required', 'integer', Rule::in([7000, 8000, 16000, 20000])],
            'delivery_date'      => 'required|date|after_or_equal:today',
            'delivery_time'      => 'required|date_format:H:i',
            'delivery_address'   => 'required|string',
            'delivery_latitude'  => 'required|numeric|between:-90,90',
            'delivery_longitude' => 'required|numeric|between:-180,180',
            'notes'              => 'nullable|string',
        ]);

        $depoLat = (float) Setting::get('depo_latitude', -6.304033);
        $depoLng = (float) Setting::get('depo_longitude', 107.157567);
        $ratePerKm = (float) Setting::get('rate_per_km', 15000);

        // PBI-08 Kalkulasi Jarak Server-side (Haversine Formula)
        $distanceKm = $this->calculateDistance(
            $depoLat,
            $depoLng,
            (float) $validated['delivery_latitude'],
            (float) $validated['delivery_longitude']
        );

        $waterPrice = (float) Setting::get('price_' . $validated['tank_capacity'], 300000);
        $deliveryFee = round($distanceKm * $ratePerKm, 2);
        $totalPrice = $waterPrice + $deliveryFee;

        $customer = Customer::where('user_id', Auth::id())->first();

        // Buat nomor pesanan unik
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $order = Order::create([
            'order_number'       => $orderNumber,
            'user_id'            => Auth::id(),
            'customer_id'        => $customer?->id,
            'tank_capacity'      => $validated['tank_capacity'],
            'delivery_date'      => $validated['delivery_date'],
            'delivery_time'      => $validated['delivery_time'],
            'delivery_address'   => $validated['delivery_address'],
            'delivery_latitude'  => $validated['delivery_latitude'],
            'delivery_longitude' => $validated['delivery_longitude'],
            'distance_km'        => $distanceKm,
            'water_price'        => $waterPrice,
            'delivery_fee'       => $deliveryFee,
            'total_price'        => $totalPrice,
            'notes'              => $validated['notes'],
            'status'             => 'pending',
        ]);

        return redirect()->route('customer.orders.show', $order)->with('success', 'Pesanan air tangki berhasil dibuat! Menunggu validasi admin/owner.');
    }

    public function show(Order $order)
    {
        if (Auth::user()->role === 'customer' && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Hitung jarak dua titik koordinat dalam KM (Haversine Formula)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Bulatkan minimal 1 km jika jarak sangat dekat
        return max(round($distance, 2), 1.0);
    }
}
