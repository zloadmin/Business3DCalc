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
        Schema::create('train_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('game_session_id')
                ->constrained('game_sessions')
                ->cascadeOnDelete();

            // City state
            $table->unsignedBigInteger('money')->default(0);

            $table->unsignedInteger('population')->default(0);
            $table->unsignedInteger('population_capacity')->default(0);
            $table->unsignedInteger('employees')->default(0);
            $table->unsignedInteger('unemployed')->default(0);

            $table->decimal('happiness', 8, 2)->default(0);
            $table->decimal('attractiveness', 8, 2)->default(0);

            // Migration
            $table->unsignedInteger('coming')->default(0);
            $table->unsignedInteger('leave')->default(0);

            // coming - leave
            $table->integer('result')->default(0);

            $table->unsignedBigInteger('game_time')->default(0);

            $table->timestamps();

            $table->index(['game_session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('train_items');
    }
};
