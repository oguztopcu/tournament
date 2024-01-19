<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }
}
