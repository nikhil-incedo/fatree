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

    public function scopeParents($query) {
        return $query->whereIn('related_type', ['father', 'mother']);
    }

    public function scopeSpouse($query) {
        return $query->where('related_type', 'spouse');
    }

}
