<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetType extends Model
{
   
    protected $primaryKey = 'carpet_type_id';
    protected $guarded = [];
    public function carpet() {
        return $this->hasMany(Carpet::class , 'type_id' , 'carpet_id');
    }

    public function quality(){
        return $this->hasMany(Quality::class,'type_id','carpet_type_id');
    }
}
