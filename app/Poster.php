<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poster extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'topic', 'description','project_name','poster_pdf_url','poster_image_url','pdf_image_url','poster_video_url','user_id'
    ];
    protected $dates = ['deleted_at'];

    public  function user() {
        return $this->belongsTo('App\User');
    }

    public function likes() {
        return  $this->hasMany('App\Like');
    }
    public function comments() {
        return  $this->hasMany('App\Comment');
    }

    public function notifications() {
        return $this->morphToMany('App\Notification','notificationable');
    }
}
