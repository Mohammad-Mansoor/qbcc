<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected  $guarded = [];
    public function carpet(){
        return $this->hasMany(Carpet::class,'package_id','id');
    }
    public function packing(){
        return $this->belongsTo(PakingList::class,'packing_id','id');
    }
}
