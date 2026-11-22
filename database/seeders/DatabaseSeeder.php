<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for Sprint 1.
     */
    public function run(): void
    {
        // 1. Akun Pengguna (PBI-01: Admin, Owner, Driver, Customer)
        $admin = User::firstOrCreate(
            ['email' => 'admin@tangki.com'],
            [
                'name' => 'Admin Operasional',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '081200000001',
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@tangki.com'],
            [
                'name' => 'Owner Bisnis Tangki',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'status' => 'active',
                'phone' => '081200000002',
            ]
        );

        $driverUser1 = User::firstOrCreate(
            ['email' => 'driver1@tangki.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'status' => 'active',
                'phone' => '081234567891',
            ]
        );

        $driverUser2 = User::firstOrCreate(
            ['email' => 'driver2@tangki.com'],
            [
                'name' => 'Joko Pranoto',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'status' => 'active',
                'phone' => '081234567892',
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@tangki.com'],
            [
                'name' => 'Pak Hendra (Hotel Sentral)',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'phone' => '081398765431',
            ]
        );

        // 2. Settings Tarif & Depo Air (PBI-06)
        $settings = [
            ['key' => 'depo_name', 'label' => 'Nama Depo Pusat', 'value' => 'Depo Utama Tirta Tangki', 'type' => 'text'],
            ['key' => 'depo_address', 'label' => 'Alamat Depo Pusat', 'value' => 'Jl. Akses Industri No. 12, Cikarang', 'type' => 'text'],
            ['key' => 'depo_latitude', 'label' => 'Koordinat Latitude Depo', 'value' => '-6.304033', 'type' => 'coordinate'],
            ['key' => 'depo_longitude', 'label' => 'Koordinat Longitude Depo', 'value' => '107.157567', 'type' => 'coordinate'],
            ['key' => 'rate_per_km', 'label' => 'Tarif Ongkir per Kilometer (Rp)', 'value' => '15000', 'type' => 'currency'],
            ['key' => 'price_7000', 'label' => 'Harga Air Tangki 7.000 Liter (Rp)', 'value' => '250000', 'type' => 'currency'],
            ['key' => 'price_8000', 'label' => 'Harga Air Tangki 8.000 Liter (Rp)', 'value' => '300000', 'type' => 'currency'],
            ['key' => 'price_16000', 'label' => 'Harga Air Tangki 16.000 Liter (Rp)', 'value' => '550000', 'type' => 'currency'],
            ['key' => 'price_20000', 'label' => 'Harga Air Tangki 20.000 Liter (Rp)', 'value' => '700000', 'type' => 'currency'],
        ];

        foreach ($settings as $item) {
            Setting::firstOrCreate(['key' => $item['key']], $item);
        }

        // 3. Master Driver (PBI-04)
        Driver::firstOrCreate(
            ['phone' => '081234567891'],
            [
                'user_id' => $driverUser1->id,
                'name' => 'Budi Santoso',
                'license_number' => 'SIM-B2-88771122',
                'status' => 'ready',
            ]
        );

        Driver::firstOrCreate(
            ['phone' => '081234567892'],
            [
                'user_id' => $driverUser2->id,
                'name' => 'Joko Pranoto',
                'license_number' => 'SIM-B2-88771133',
                'status' => 'ready',
            ]
        );

        Driver::firstOrCreate(
            ['phone' => '081234567893'],
            [
                'user_id' => null,
                'name' => 'Agus Setiawan',
                'license_number' => 'SIM-B1-77665544',
                'status' => 'off',
            ]
        );

        // 4. Master Armada Truk Tangki (PBI-05: 7k, 8k, 16k, 20k L)
        $trucks = [
            ['plate_number' => 'B 9101 TKA', 'brand' => 'Hino Dutro 130 HD', 'capacity' => 7000, 'status' => 'ready', 'notes' => 'Kondisi prima, baru uji KIR'],
            ['plate_number' => 'B 9102 TKB', 'brand' => 'Mitsubishi Fuso Canter', 'capacity' => 8000, 'status' => 'ready', 'notes' => 'Siap operasi pengiriman dekat & jauh'],
            ['plate_number' => 'B 9103 TKC', 'brand' => 'Isuzu Giga FVR', 'capacity' => 16000, 'status' => 'ready', 'notes' => 'Kapasitas besar untuk proyek & pabrik'],
            ['plate_number' => 'B 9104 TKD', 'brand' => 'Hino Ranger FL', 'capacity' => 20000, 'status' => 'maintenance', 'notes' => 'Sedang penggantian kampas rem & ganti oli'],
        ];

        foreach ($trucks as $truck) {
            Truck::firstOrCreate(['plate_number' => $truck['plate_number']], $truck);
        }

        // 5. Master Data Pelanggan & Kategori Bisnis (PBI-02 & PBI-03)
        $customer1 = Customer::firstOrCreate(
            ['whatsapp_number' => '081398765431'],
            [
                'user_id' => $customerUser->id,
                'name' => 'Hotel Grand Sentral',
                'business_category' => 'Hotel',
                'address' => 'Jl. MH Thamrin No. 88, Pusat Kota',
                'latitude' => -6.2951200,
                'longitude' => 107.1654300,
            ]
        );

        Customer::firstOrCreate(
            ['whatsapp_number' => '081398765432'],
            [
                'name' => 'PT Baja Mandiri Perkasa',
                'business_category' => 'Pabrik',
                'address' => 'Kawasan Industri MM2100 Blok C-4',
                'latitude' => -6.3120000,
                'longitude' => 107.1350000,
            ]
        );

        Customer::firstOrCreate(
            ['whatsapp_number' => '081398765433'],
            [
                'name' => 'Proyek Flyover Deltamas',
                'business_category' => 'Proyek',
                'address' => 'Kavling Proyek Konstruksi Simpang 3',
                'latitude' => -6.3350000,
                'longitude' => 107.1750000,
            ]
        );

        Customer::firstOrCreate(
            ['whatsapp_number' => '081398765434'],
            [
                'name' => 'Restoran Sari Rasa',
                'business_category' => 'Restoran',
                'address' => 'Ruko Sentra Kuliner No. 12',
                'latitude' => -6.2980000,
                'longitude' => 107.1520000,
            ]
        );

        // 6. Contoh Order Awal Customer (PBI-07 & PBI-08)
        Order::firstOrCreate(
            ['order_number' => 'ORD-' . date('Ymd') . '-0001'],
            [
                'user_id' => $customerUser->id,
                'customer_id' => $customer1->id,
                'tank_capacity' => 8000,
                'delivery_date' => now()->addDay()->toDateString(),
                'delivery_time' => '10:00:00',
                'delivery_address' => 'Hotel Grand Sentral, Lobby Utama, Jl. MH Thamrin No. 88',
                'delivery_latitude' => -6.2951200,
                'delivery_longitude' => 107.1654300,
                'distance_km' => 3.5,
                'water_price' => 300000,
                'delivery_fee' => 52500, // 3.5 * 15000
                'total_price' => 352500,
                'notes' => 'Harap pengiriman tepat waktu sebelum jam check-in hotel',
                'status' => 'pending',
            ]
        );
    }
}
