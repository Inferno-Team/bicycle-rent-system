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
        'old_stand_id',
        'last_stand_id',
        'price',
        'distence',
        'time',
    ];

    function format(){
        return [
            'id' =>$this->id,
            'bicycle' =>$this->bicycle,
            'user' =>$this->user,
            'old_stand' =>$this->old_stand,
            'last_stand' =>$this->last_stand,
            'price' =>$this->price,
            'distence' =>$this->distence,
            'time' =>$this->time,
            'created_at' =>$this->created_at->diffForHumans(),
        ];
    }
    public function bicycle()
    {
        return $this->belongsTo(Bicycle::class, 'bicycle_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function old_stand()
    {
        return $this->belongsTo(Stand::class, 'old_stand_id');
    }
    public function last_stand()
    {
        return $this->belongsTo(Stand::class, 'last_stand_id');
    }
}
