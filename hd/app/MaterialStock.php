<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MaterialStock extends Model 
{
  
    protected $fillable = ['material_type' , 'material_category' , 'quantity' , 'price_per_kilo' , 'currency','in_words'];

    public function category() {
        return $this->belongsTo(MaterialCategory::class , 'material_category' , 'material_category_id');
    }

    public function type() {
        return $this->belongsTo(MaterialType::class , 'material_type' , 'material_type_id');
    }

}
