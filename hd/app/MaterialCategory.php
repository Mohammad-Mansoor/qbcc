<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MaterialCategory extends Model
{
  
    protected $primaryKey = 'material_category_id';
    protected $guarded = [];

    public function purchase() {
        return $this->hasMany(PurchaseMaterial::class , 'material_category' , 'id');
    }

    public function stock() {
        return $this->hasMany(MaterialStock::class , 'material_category' , 'id');
    }
    public function material() {
        return $this->hasMany(CarpetMaterial::class , 'category_id', 'material_category_id');
    }
    public function sale() {
        return $this->hasMany(MaterialSale::class , 'category_id', 'material_category_id');
    }
}
