<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DifferentAccountTotal extends Model
{
    public function account(){
        return $this->belongsTo(DifferentAccount::class,'account_id','id');
    }
}
