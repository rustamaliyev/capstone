<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Working extends Model
{


    public function user() {

        return $this->belongsTo(User::class,'changedByUserID');
    }
    
    public function working() {

        return $this->belongsTo(Staging::class,'workingID');
    }

   
}
