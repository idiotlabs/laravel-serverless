<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeTrend extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'video_id',
        'published_at',
        'channel_title',
        'video_views',
        'video_duration',
        'count',
        'rank',
    ];
}
