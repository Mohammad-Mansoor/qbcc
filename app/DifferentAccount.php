<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DifferentAccount extends Model
{
    protected $guarded  = [];




    public function totals(){
        return $this->hasMany(DifferentAccountTotal::class,'account_id','id');
    }
    public function payment(){
        return $this->hasMany(DifferentAccountPayment::class,'account_id','id');
    }
}
