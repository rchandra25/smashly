<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'confirmed', 'checked_in', 'no_show'])->default('registered');
            $table->timestamps();

            $table->unique(['game_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_players');
    }
};
