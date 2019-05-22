<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name', 'email', 'password','country_name','email_verified_at','facebook_id','google_id','image_url','bio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $dates = ['deleted_at'];

    public function posters() {
        return $this->hasMany('App\Poster');
    }

    public function favourites() {
        return $this->hasMany('App\Favourite');
    }
    public function followers() {
        return $this->belongsToMany('App\User','followers','user_id','follower_id');
    }
    public function following() {
        return $this->belongsToMany('App\User','followers','follower_id','user_id');
    }

    public function messages() {
        return $this->hasMany('App\Message','sender_id');
    }
}
