<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Sale extends Model 
{
    
    protected $guarded = [];

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function carpet(){
        return $this->belongsTo(Carpet::class,'carpet_id','carpet_id');
    }

}
