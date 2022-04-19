<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bicycle_id',
        'old_stand',
        'last_stand',
        'price',
        'distence',
        'time',
    ];
    public function bicycle()
    {
        return $this->hasOne(Bicycle::class, 'bicycle_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
    public function old_stand()
    {
        return $this->hasOne(Stand::class, 'old_stand');
    }
    public function last_stand()
    {
        return $this->hasOne(Stand::class, 'last_stand');
    }
}
