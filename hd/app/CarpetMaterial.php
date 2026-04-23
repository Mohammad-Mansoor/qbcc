<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CarpetMaterial extends Model
{

    protected $guarded = [];

    public function carpet(){
        return $this->belongsTo(Carpet::class,'carpet_id','carpet_id');
    }
    public function category(){
        return $this->belongsTo(MaterialCategory::class, 'category_id', 'material_category_id');
    }
    public function type(){
        return $this->belongsTo(MaterialType::class, 'type_id', 'material_type_id');
    }
}
