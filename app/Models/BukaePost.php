<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukaePost extends Model
{
    protected $table = 'bukae_posts';
    public $timestamps = false; // created_at은 DB default(useCurrent)로 채움

    protected $fillable = [
        'device_id', 'fc', 'fs', 'e', 'm', 'h', 'archetype', 'tagline',
    ];
}
