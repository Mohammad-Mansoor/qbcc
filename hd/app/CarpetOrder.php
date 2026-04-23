<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetOrder extends Model
{
 
    public function carpet() {
        return $this->hasMany(Carpet::class , 'order_id' , 'id');
    }
}
