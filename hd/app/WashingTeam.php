<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WashingTeam extends Model
{
   
    protected $guarded = [];


    
    public function carpet_wash(){
        return $this->hasMany(CarpetWash::class,'team_id');
    }
    
    public function carpet(){
        return $this->hasMany(Carpet::class,'washing_id');
    }

    public function payment(){
        return $this->hasMany(WashingPayment::class,'team_id','id');
    }


}
