<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Securitylog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'action',
        'ip_adress'
    ];
    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

}
