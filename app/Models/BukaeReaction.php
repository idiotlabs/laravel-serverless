<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukaeReaction extends Model
{
    protected $table = 'bukae_reactions';
    public $timestamps = false;

    protected $fillable = ['post_id', 'device_id', 'emoji'];
}
