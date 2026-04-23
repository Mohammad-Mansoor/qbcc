<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class KachaeePayment extends Model
{
   
    protected $guarded = [];
    public function kachaee(){
        return $this->belongsTo(Kachaee::class,'team_id','id');
    }
}
