<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class YoutubeTrendUser extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'user_token',
        'platform',
        'device_id',
    ];

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
//        return 'd9gmcXuJS2urF4bdViuHyF:APA91bHLKNsyCP0F717ADKp4_wvmk6I4nECYs4sLL82eQIDg40cF1u9otN5wbwlzcRtyCg91GqzwtXu7tI_vI2Y623XpbpC2xWzLW0FyenNjh2nAfL4itJflrHAfCErU7VVeC7x8vkUw';
    }
}
