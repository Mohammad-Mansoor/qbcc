<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Kachaee extends Model 
{
   
    protected $guarded = [];
    public function carpet()
    {
        return $this->hasOne(Carpet::class,'kachee_id','id');
    }
    public function repair(){
        return $this->hasOne(CarpetRepair::class,'team_id','id');
    }

    public function payment(){
        return $this->hasMany(KachaeePayment::class,'team_id','id');
    }


}
