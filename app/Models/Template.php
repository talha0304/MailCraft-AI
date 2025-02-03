<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    protected $fillable = [
       'name',
       'content',
       'category',
       'user_id'
    ];


    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }


}
