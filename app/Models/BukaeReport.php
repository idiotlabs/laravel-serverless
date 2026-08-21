<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukaeReport extends Model
{
    protected $table = 'bukae_reports';
    public $timestamps = false;

    protected $fillable = ['post_id', 'device_id'];
}
