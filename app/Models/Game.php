<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_one_id',
        'player_two_id',
        'state',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'json',
        ];
    }

    public function playerOne(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playerTwo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
