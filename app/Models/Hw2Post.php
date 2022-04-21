<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hw2Post extends Model
{
    protected $table = "hw2_posts";

    protected $fillable = [
        'creator', 'title', 'url_img', 'date_and_time',
    ];

    protected $hidden = [
        'creator'
    ];

    public function user() {
        return $this->belongsTo("App\User", "creator", "username");
    }

    public function likeUsers() {
        return $this->belongsToMany("App\User", "hw2_likes", "post", "username");
    }
}
