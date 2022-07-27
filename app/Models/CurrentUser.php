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

    function format(){
        return [
            'id' =>$this->id,
            'bicycle' =>$this->bicycle,
            'user' =>$this->user,
            'lat' =>$this->lat,
            'long' =>$this->long,
            'created_at' =>$this->created_at->diffForHumans(),
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function bicycle()
    {
        return $this->belongsTo(Bicycle::class, 'bicycle_id');
    }
}
