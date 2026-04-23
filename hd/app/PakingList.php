<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class PakingList extends Model 
{
   
    protected $guarded = [];

    public function package(){
        return $this->hasMany(Package::class,'packing_id','id');
    }
}
