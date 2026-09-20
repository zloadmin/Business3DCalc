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
            $table->string('name');
            $table->string('type');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('income')->default(0);
            $table->decimal('upgrade_coefficient', 8, 3)->default(1.000);
            $table->unsignedInteger('employees_count')->default(0);
            $table->unsignedInteger('residents_count')->default(0);
            $table->integer('happiness')->default(0);
            $table->integer('attractiveness')->default(0);
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
