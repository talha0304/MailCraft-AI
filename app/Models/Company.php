<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model 
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'url',
        'company_logo',
        'password',
        'otp',
        'otp_created_at',
        'is_verfied	',
    ];
    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }
}
