<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    public function statistics(): HasOne
    {
        return $this->hasOne(Participant::class);
    }
}
