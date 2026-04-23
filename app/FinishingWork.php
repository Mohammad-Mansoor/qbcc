<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class FinishingWork extends Model
{
 
    protected $guarded = [];

    public function carpet(){
        return $this->belongsTo(Carpet::class,'carpetId','carpet_id');
    }
    
    public function team(){
        return $this->belongsTo(FinishingTeam::class,'team_id','id');
    }
    
    public function category(){
        return $this->belongsTo(FinishingTeamCategory::class,'category_id','id');
    }
}
