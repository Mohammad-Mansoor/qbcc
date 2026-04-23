<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DifferentAccount extends Model
{
    protected $guarded  = [];




    public function total(){
        return $this->hasOne(DifferentAccountTotal::class,'account_id','id');
    }
    public function payment(){
        return $this->hasMany(DifferentAccountPayment::class,'account_id','id');
    }
}
