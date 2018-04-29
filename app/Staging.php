<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staging extends Model
{

    public $table = "staging";

    public function user() {

        return $this->belongsTo(User::class,'userID');
    }

   
}
