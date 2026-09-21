<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('building_time')->default(0);
            $table->string('name');
            $table->string('type');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('income')->default(0);
            $table->decimal('upgrade_time_coefficient', 8, 3)->default(5.000);
            $table->decimal('upgrade_price_coefficient', 8, 3)->default(1.2);
            $table->unsignedInteger('employees')->default(0);
            $table->unsignedInteger('residents')->default(0);
            $table->unsignedInteger('residents_capacity')->default(0);
            $table->decimal('happiness', 2)->default(0);
            $table->decimal('attractiveness', 2)->default(0);
            $table->unsignedInteger('city_capacity')->default(100);
            $table->unsignedInteger('city_population')->default(70);
            $table->timestamps();

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
