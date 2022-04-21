<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = "username";
    public $incrementing = false;
    protected $keyType = "string";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'surname', 'email', 'password', 'photo',
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

    public function posts() {
        return $this->hasMany("App\Models\Hw2Post", "creator", "username");
    }

    public function likePosts() {
        return $this->belongsToMany("App\Models\Hw2Post", "hw2_likes", "username", "post");
    }

    public function users() {
        return $this->belongsToMany("App\User", "hw2_followers", "user_username", "user_followed");
    }

    public function followedUsers() {
        return $this->belongsToMany("App\User", "hw2_followers", "user_followed", "user_username");
    }

    public function setPhotoAttribute($value){
        $path = $value ? Storage::disk("public")->put("userImage", $value) : null;
        $this->attributes["photo"] = $path;
    }

    public function getPhotoAttribute($value) {
        return $value ? Storage::disk("public")->url($value) : asset("images\default.png");
    }
}


