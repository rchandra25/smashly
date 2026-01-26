<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elo_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('elo_rating');
            $table->integer('elo_change')->default(0);
            $table->foreignId('match_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reason')->nullable(); // 'match_win', 'match_loss', 'initial'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elo_history');
    }
};
