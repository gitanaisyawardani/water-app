<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PBI-05: Master Data Armada Truk Tangki (7k, 8k, 16k, 20k Liter & Ready/On Duty/Maintenance)
     */
    public function up(): void
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 20)->unique();
            $table->string('brand', 100)->nullable();
            $table->unsignedInteger('capacity')->comment('Kapasitas dalam Liter: 7000, 8000, 16000, 20000');
            $table->enum('status', ['ready', 'on_duty', 'maintenance'])->default('ready');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
