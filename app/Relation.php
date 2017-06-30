<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    //
    public function primaryUser() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function secondryUser() {
        return $this->hasOne('App\User', 'id', 'related_id');
    }
}
