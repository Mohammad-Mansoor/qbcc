<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Customer extends Model 
{
  
    protected $guarded = [];
    public function invoice(){
        return $this->hasMany(Invoice::class,'customer_id');
    }
    public function sale(){
        return $this->hasMany(Sale::class,'customer_id');
    }
    public function payment(){
        return $this->hasMany(CustomerPayment::class,'customer_id');
    }

}
