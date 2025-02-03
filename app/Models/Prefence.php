<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefence extends Model
{
    use HasFactory;
    protected $fillable = [
        'tone',
        'propose'     
    ];


    public function genratedEmail()
    {
        $this->belongsTo(GenratedEmail::class,'prefence_id'); 
    }


}
