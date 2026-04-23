<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialAccount extends Model
{
    public function payment(){
        return $this->hasMany(MaterialAccountPayment::class,'account_id','id');
    }
    protected $guarded = [];
}
