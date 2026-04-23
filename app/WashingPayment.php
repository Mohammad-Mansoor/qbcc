<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WashingPayment extends Model
{
   
    protected $guarded = [];
    public function team(){
        return $this->belongsTo(WashingTeam::class,'team_id','id');
    }
}
