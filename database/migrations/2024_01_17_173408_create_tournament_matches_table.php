<?php

use App\Enums\MatchStatus;
use App\Enums\WinType;
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
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_1')->constrained('teams');
            $table->foreignId('team_2')->constrained('teams');
            $table->foreignId('winner_id')->nullable();
            $table->tinyInteger('week')->default(1);
            $table->unsignedTinyInteger('team_1_score')->default(0);
            $table->unsignedTinyInteger('team_2_score')->default(0);
            $table->enum('win_type', WinType::getValues())->default(WinType::UNKNOWN->value);
            $table->enum('status', MatchStatus::getValues())->default(MatchStatus::PENDING->value);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
