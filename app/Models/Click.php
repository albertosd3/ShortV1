<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id', 'ip', 'country', 'device', 'browser', 'user_agent', 'referer', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }
}
