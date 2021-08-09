<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    protected $fillabel = [
        'title',
        'category_id',
        'user_id',
        'short_desc',
        'long_desc',
        'special',
        'breaking',
        'views'
    ];

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function tags(){
        return $this->hasMany('App\Models\Tags');
    }
}
