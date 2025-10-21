<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'destination', 'is_rotator', 'meta'
    ];

    protected $casts = [
        'is_rotator' => 'boolean',
        'meta' => 'array',
    ];

    public function destinations()
    {
        return $this->hasMany(LinkDestination::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
