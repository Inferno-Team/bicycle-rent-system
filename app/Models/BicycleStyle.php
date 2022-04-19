<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BicycleStyle extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'color', 'size'
    ];

    public function bicycles()
    {
        return $this->hasMany(Bicycle::class, 'style_id');
    }
}
