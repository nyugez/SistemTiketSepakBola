<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'capacity',
    ];

    // Satu stadion bisa dipakai banyak pertandingan
    public function footballMatches()
    {
        return $this->hasMany(FootballMatch::class);
    }
}
