<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetTransport extends Model 
{
  
    public function carpet() {
        return $this->hasMany(Carpet::class , 'transport_id' , 'carpet_id');
    }
}
