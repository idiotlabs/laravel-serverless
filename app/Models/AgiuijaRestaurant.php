<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgiuijaRestaurant extends Model
{
    protected $table = 'agiuija_restaurants';

    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id', 'name', 'address', 'lat', 'lng',
        'hasHighChair', 'hasBowlAndSpoon', 'notes',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::random(21);
            }
        });
    }
}
