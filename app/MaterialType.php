<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MaterialType extends Model 
{
  
    protected $primaryKey = "material_type_id";
    protected $guarded = [];

    public function purchase()
    {
        return $this->hasMany(PurchaseMaterial::class,'material_type','id');
    }

    public function stock() {
        return $this->hasMany(MaterialStock::class , 'material_type' , 'id');
    }
    public function carpet_material(){
        return $this->hasMany(CarpetMaterial::class, 'type_id', 'material_type_id');
    }
    public function sale() {
        return $this->hasMany(MaterialSale::class , 'type_id', 'material_type_id');
    }

    public function getSubtypeFaAttribute()
    {
        $map = [
            'yarn' => 'تار',
            'dye'  => 'رنگ',
        ];
        return $map[$this->subtype] ?? $this->subtype;
    }
}

