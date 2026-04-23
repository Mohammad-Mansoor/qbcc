<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class FinishingTeamPayment extends Model 
{ 
    protected $guarded = [];
    public function team(){
        return $this->belongsTo(FinishingTeam::class,'team_id','id');
    }
}
