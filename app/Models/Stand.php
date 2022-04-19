<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stand extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'location', 'lat', 'long', 'bicycle_count'
    ];
    public function bicycles()
    {
        return $this->hasMany(Bicycle::class, 'stand_id');
    }
}
