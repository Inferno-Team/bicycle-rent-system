<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bicycle extends Model
{
    use HasFactory;


   
    protected $fillable = [
        'name', 'lat', 'long', 'price_per_time',
        'price_per_distance', 'style_id', 'stand_id', 'esp32_id',
        'is_available', 'img_url'
    ];

    public function style()
    {
        return $this->belongsTo(BicycleStyle::class, 'style_id');
    }
    public function stand()
    {
        return $this->belongsTo(Stand::class, 'stand_id');
    }
    public function esp32()
    {
        return $this->belongsTo(User::class, 'esp32_id');
    }

    public function current_user()
    {
        return $this->hasOneThrough(User::class, CurrentUser::class, 'user_id', 'id');
    }
    protected $casts = [
        'is_available' => 'boolean',
        'is_sport' => 'boolean',
    ];
}
