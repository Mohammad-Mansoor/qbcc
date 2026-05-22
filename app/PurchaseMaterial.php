<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseMaterial extends Model
{
 
    protected $guarded = [];
    public function materialType() {
        return $this->belongsTo(MaterialType::class , 'material_type' , 'material_type_id');
    }

    public function materialCategory() {
        return $this->belongsTo(MaterialCategory::class , 'material_category' , 'material_category_id');
    }

    public function seller() {
        return $this->belongsTo(StringSeller::class , 'seller_id' , 'id');
    }

    public function warehouse() {
        return $this->belongsTo(\App\Warehouse::class, 'warehouse_id', 'id');
    }
}
