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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('played')->default(0);
            $table->unsignedTinyInteger('win')->default(0);
            $table->unsignedTinyInteger('draw')->default(0);
            $table->unsignedTinyInteger('lose')->default(0);
            $table->unsignedTinyInteger('goal_score')->default(0);
            $table->unsignedTinyInteger('goal_conceded')->default(0);
            $table->unsignedFloat('predictions')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
