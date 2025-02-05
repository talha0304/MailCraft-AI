<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Security_log extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'action',
        'ip_address'
    ];
    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

}
