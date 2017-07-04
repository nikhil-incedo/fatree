<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //use Notifiable;

    public function relations() {
        return $this->hasMany('App\Relation');
    }

    public function Property() {
        return $this->hasOne('App\Property');
    }
}
