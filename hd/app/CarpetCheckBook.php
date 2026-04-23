<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetCheckBook extends Model 
{
 
    protected $guarded = [];
    public function carpet() {
        return $this->belongsTo(Carpet::class , 'carpet_id' , 'carpet_id');
    }
}
