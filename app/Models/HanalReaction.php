<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HanalReaction extends Model
{
    protected $table = 'hanal_reactions';
    public $timestamps = false;

    protected $fillable = ['meditation_id', 'device_id', 'reaction'];
}
