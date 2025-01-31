<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'username',
        'email',
        'age',
        'password',
        'otp',
        'otp_created_at',
        'is_verfied	'
    ];

    public function genratedEmails()
    {
        $this->hasMany(GenratedEmail::class, 'user_id');
    }


}
