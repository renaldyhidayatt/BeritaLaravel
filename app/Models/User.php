<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country',
        'address',
        'image',
        'profession',
        'bio',
        'state'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function categories(){
        return $this->hasMany('App\Models\Category');
    }

    public function posts(){
        return $this->hasMany('App\Models\Posts');
    }

    public function writter(){
        return $this->hasOne('App\Models\Writter');
    }

    public function advert(){
        return $this->hasOne('App\Models\Advertise');
    }

    public function videos(){
        return $this->hasMany('App\Models\Video');
    }
}
