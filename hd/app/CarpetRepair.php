<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetRepair extends Model
{
  
    protected $guarded = [];
    public function team() {
        return $this->belongsTo(Kachaee::class , 'team_id' , 'id');
    }

    public function carpet() {
        return $this->belongsTo(Carpet::class , 'carpetId' , 'carpet_id');
    }
}
