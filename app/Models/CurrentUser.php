<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'bicycle_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
    public function bicycle()
    {
        return $this->hasOne(Bicycle::class, 'bicycle_id');
    }
}
