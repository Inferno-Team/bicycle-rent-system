<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'cause'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function format()
    {
        return [
            'user' => $this->user,
            'cause' => $this->cause,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
