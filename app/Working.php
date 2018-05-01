<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Working extends Model
{

    public $table = "working";
    public function user() {

        return $this->belongsTo(User::class,'userID');
    }
    
    public function staging() {

        return $this->belongsTo(Staging::class,'stagingID');
    }

   
}
