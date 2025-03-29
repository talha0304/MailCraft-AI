<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class language extends Model
{
    protected $fillable = [
        'user_id',
        'language'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function genEmail(){
        return $this->hasMany(GenratedEmail::class,'language_id');
    }
}
