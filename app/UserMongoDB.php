<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class UserMongoDB extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'mdb_posts';
    protected $primaryKey = 'username';
}
