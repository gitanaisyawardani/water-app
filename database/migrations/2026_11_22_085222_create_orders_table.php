<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PBI-07: Pemesanan Air Tangki Mandiri (Customer)
     * PBI-08: Kalkulasi Otomatis Biaya Pesanan: (Volume x Harga) + (Jarak KM x Tarif KM)
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->nullOnDelete();
            
            $table->unsignedInteger('tank_capacity')->comment('Volume liter: 7000, 8000, 16000, 20000');
            $table->date('delivery_date');
            $table->time('delivery_time');
            $table->text('delivery_address');
            $table->decimal('delivery_latitude', 10, 7);
            $table->decimal('delivery_longitude', 10, 7);
            
            $table->decimal('distance_km', 8, 2)->default(0)->comment('Jarak tempuh dari depo air');
            $table->decimal('water_price', 12, 2)->comment('Biaya air sesuai kapasitas');
            $table->decimal('delivery_fee', 12, 2)->comment('Ongkos kirim = KM * Tarif');
            $table->decimal('total_price', 12, 2)->comment('Total = water_price + delivery_fee');
            
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'delivering', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
