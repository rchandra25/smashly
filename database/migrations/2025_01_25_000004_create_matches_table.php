<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('player1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('player2_id')->constrained('users')->onDelete('cascade');
            $table->integer('player1_score')->nullable();
            $table->integer('player2_score')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'disputed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            // Elo snapshot for explanation
            $table->integer('player1_elo_before')->nullable();
            $table->integer('player2_elo_before')->nullable();
            $table->integer('player1_elo_change')->nullable();
            $table->integer('player2_elo_change')->nullable();
            $table->decimal('expected_probability', 5, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
