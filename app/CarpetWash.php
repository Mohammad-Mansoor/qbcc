<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetWash extends Model
{
   
    protected $guarded = [];

    public function washing_team(){
        return $this->belongsTo(WashingTeam::class,'team_id');
    }

    public function carpet(){
        return $this->belongsTo(Carpet::class,'carpetId');
    }
}
