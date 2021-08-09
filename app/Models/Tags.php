<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'post_id',
        'tag'
    ];
    public function post()
    {
        return $this->belongsTo('App\Models\Posts');
    }
}
