<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('tournaments.index'));

Route::prefix('tournaments')->group(function () {
    Route::get('/', [TournamentController::class, 'index'])->name('tournaments.index');
    Route::post('/', [TournamentController::class, 'store'])->name('tournaments.store');

    Route::get('/{tournamentId}', [TournamentController::class, 'show'])->name('tournaments.show');
    Route::get('/{tournamentId}/simulation', [TournamentController::class, 'simulation'])->name('tournaments.simulation');
    
    Route::post('/{tournamentId}/play', [TournamentController::class, 'play'])->name('tournaments.play');
    Route::post('/{tournamentId}/reset', [TournamentController::class, 'reset'])->name('tournaments.reset');
});
