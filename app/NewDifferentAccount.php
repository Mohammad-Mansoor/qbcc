<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewDifferentAccount extends Model
{
    protected $guarded  = [];

    public function payment(){
        return $this->hasMany(NewDifferentAccountPayment::class,'account_id','id');
    }
}
