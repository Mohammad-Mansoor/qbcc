<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class FinishingTeam extends Model
{
 
    protected $guarded = [];
    
    public function work() {
        return $this->hasMany(FinishingWork::class , 'finishing_id' , 'id');
    }

    public function payment(){
        return $this->hasMany(FinishingTeamPayment::class,'team_id','id');
    }
}
