<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\language;
use App\Models\Security_log;
use App\Models\GenratedEmail;
use App\Models\Template;
use App\Models\Company;

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
        'is_verfied	',
        'company_id'
    ];

    public function genratedEmails()
    {
        return $this->hasMany(GenratedEmail::class, 'user_id');
    }


    public function securitylogs()
    {
        return $this->hasMany(Security_log::class, 'user_id');
    }

    public function emailTemplates()
    {
        return $this->hasMany(Template::class, 'user_id');
    }

    public function company()
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    public function languages (){
        return $this->hasMany(Language::class,'user_id');
    }

}
