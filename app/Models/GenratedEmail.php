<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenratedEmail extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'recipient_email',
        'cc',
        'email_subject',
        'description',
        'content',
    ];

    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

}
