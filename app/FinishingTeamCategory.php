<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class FinishingTeamCategory extends Model
{
   
    protected $guarded =[];

    public function work(){
        return $this->hasMany(FinishingWork::class,'category_id','id');
    }
}
