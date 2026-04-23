<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    protected $guarded = [];

    public function carpet(){
        return $this->hasMany(Carpet::class,'quality_id','id');
    }
    public function type(){
        return $this->belongsTo(CarpetType::class,'type_id','carpet_type_id');
    }
}
