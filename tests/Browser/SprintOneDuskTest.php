<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SprintOneDuskTest extends DuskTestCase
{
    /**
     * Dusk Test: Admin login dan navigasi Master Data (PBI-01 s/d PBI-06)
     */
    public function test_admin_navigation_and_master_data(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@tangki.com')
                    ->type('password', 'password')
                    ->press('LOG IN')
                    ->assertPathIs('/admin/drivers')
                    ->assertSee('Master Data Driver')
                    ->assertSee('Budi Santoso')
                    
                    // Kunjungi Halaman Armada Truk (PBI-05)
                    ->visit('/admin/trucks')
                    ->assertSee('Master Data Armada Truk Tangki')
                    ->assertSee('B 9101 TKA')

                    // Kunjungi Halaman Pelanggan (PBI-02 & PBI-03)
                    ->visit('/admin/customers')
                    ->assertSee('Master Data Pelanggan')
                    ->assertSee('Hotel Grand Sentral')

                    // Kunjungi Pengaturan Tarif (PBI-06)
                    ->visit('/admin/settings')
                    ->assertSee('Konfigurasi Harga Dasar Air');
        });
    }

    /**
     * Dusk Test: Customer pemesanan air tangki & kalkulasi real-time (PBI-07 & PBI-08)
     */
    public function test_customer_can_order_water(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'customer@tangki.com')
                    ->type('password', 'password')
                    ->press('LOG IN')
                    ->assertPathIs('/customer/orders/create')
                    ->assertSee('Pemesanan Air Tangki Mandiri')
                    ->assertSee('Rincian Estimasi Biaya')
                    ->press('Konfirmasi & Buat Pesanan')
                    ->assertSee('Pesanan air tangki berhasil dibuat!')
                    ->assertSee('Detail Pesanan #');
        });
    }
}
