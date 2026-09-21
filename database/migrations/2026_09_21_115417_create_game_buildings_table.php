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
        Schema::create('game_buildings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('game_session_id');
            $table->foreign('game_session_id')->references('id')->on('game_sessions')->onDelete('cascade');

            $table->unsignedBigInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->unsignedInteger('level')->default(1);
            $table->bigInteger('full_income')->default(0);

            $table->unsignedBigInteger('built_at')->nullable();
            $table->unsignedBigInteger('upgrade_finish_at')->nullable();
            $table->unsignedBigInteger('next_income_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_buildings');
    }
};
