<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MaterialSale extends Model 
{

    protected  $guarded =[];
    public function category() {
        return $this->belongsTo(MaterialCategory::class , 'category_id', 'material_category_id');
    }
    public function type() {
        return $this->belongsTo(MaterialType::class , 'type_id', 'material_type_id');
    }
    public function agent(){
        return $this->belongsTo(Agents::class,'agent_id','agent_id');

    }
}
