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
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('money')->default(0);
            $table->unsignedBigInteger('population')->default(0);
            $table->unsignedBigInteger('game_time')->default(0);
            $table->timestamp('last_tick_at')->nullable();
            $table->decimal('time_speed', 8, 2)->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
