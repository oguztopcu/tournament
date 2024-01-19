<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentMatch extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_1');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_2');
    }
}
