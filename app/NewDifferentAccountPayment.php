<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewDifferentAccountPayment extends Model
{
    protected $guarded = [];
    public function account(){
        return $this->belongsTo(NewDifferentAccount::class,'account_id','id');
    }
}
