<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SprintOneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * PBI-01: Management User & Hak Akses
     */
    public function test_admin_can_access_user_management(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }

    public function test_customer_cannot_access_admin_user_management(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get('/admin/users');
        $response->assertStatus(403);
    }

    /**
     * PBI-02 & PBI-03: Master Pelanggan & Kategori Bisnis
     */
    public function test_admin_can_create_customer_with_business_category_and_coordinates(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/customers', [
            'name' => 'Hotel Horizon Baru',
            'whatsapp_number' => '081299887766',
            'business_category' => 'Hotel',
            'address' => 'Jl. Boulevard No. 10',
            'latitude' => -6.2991000,
            'longitude' => 107.1650000,
        ]);

        $response->assertRedirect('/admin/customers');
        $this->assertDatabaseHas('customers', [
            'name' => 'Hotel Horizon Baru',
            'business_category' => 'Hotel',
            'whatsapp_number' => '081299887766',
        ]);
    }

    /**
     * PBI-04: Master Data Driver & Ketersediaan
     */
    public function test_admin_can_create_driver(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/drivers', [
            'name' => 'Ahmad Suhendar',
            'phone' => '085511223344',
            'license_number' => 'SIM-B2-112233',
            'status' => 'ready',
        ]);

        $response->assertRedirect('/admin/drivers');
        $this->assertDatabaseHas('drivers', [
            'name' => 'Ahmad Suhendar',
            'license_number' => 'SIM-B2-112233',
            'status' => 'ready',
        ]);
    }

    /**
     * PBI-05: Master Data Armada Truk Tangki
     */
    public function test_admin_can_create_truck_with_capacity(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/trucks', [
            'plate_number' => 'B 9999 TKT',
            'brand' => 'Isuzu Giga 16k',
            'capacity' => 16000,
            'status' => 'ready',
            'notes' => 'Armada baru tiba',
        ]);

        $response->assertRedirect('/admin/trucks');
        $this->assertDatabaseHas('trucks', [
            'plate_number' => 'B 9999 TKT',
            'capacity' => 16000,
            'status' => 'ready',
        ]);
    }

    /**
     * PBI-06: Konfigurasi Harga Dasar Air & Tarif KM
     */
    public function test_admin_can_update_rates_and_depo_settings(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'depo_name' => 'Depo Pusat Karawang Barat',
            'depo_address' => 'Jl. Tol Jakarta-Cikampek KM 47',
            'depo_latitude' => -6.310000,
            'depo_longitude' => 107.250000,
            'rate_per_km' => 20000,
            'price_7000' => 270000,
            'price_8000' => 320000,
            'price_16000' => 600000,
            'price_20000' => 750000,
        ]);

        $response->assertRedirect('/admin/settings');
        $this->assertEquals('20000', Setting::get('rate_per_km'));
        $this->assertEquals('320000', Setting::get('price_8000'));
    }

    /**
     * PBI-07 & PBI-08: Order Air Mandiri Customer & Kalkulasi Otomatis
     */
    public function test_customer_can_create_water_order_with_calculated_price(): void
    {
        $customerUser = User::where('role', 'customer')->first();

        $response = $this->actingAs($customerUser)->post('/customer/orders', [
            'tank_capacity' => 8000,
            'delivery_date' => now()->addDays(2)->toDateString(),
            'delivery_time' => '14:00',
            'delivery_address' => 'Gedung Pertemuan Graha Lestari, Jl. Kenanga 12',
            'delivery_latitude' => -6.2950000,
            'delivery_longitude' => 107.1650000,
            'notes' => 'Tolong telepon sebelum sampai',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $customerUser->id,
            'tank_capacity' => 8000,
            'status' => 'pending',
        ]);

        $order = Order::where('user_id', $customerUser->id)->latest('id')->first();
        $this->assertNotNull($order);
        // Formula: Total = water_price + delivery_fee
        $this->assertEquals($order->total_price, $order->water_price + $order->delivery_fee);
        $this->assertGreaterThan(0, $order->distance_km);

        $response->assertRedirect(route('customer.orders.show', $order));
    }
}
