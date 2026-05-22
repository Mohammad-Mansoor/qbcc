<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialAccountPayment extends Model
{
    protected  $guarded =[];

    public function account(){
        return $this->belongsTo(MaterialAccount::class,'account_id','id');
    }
    public function materialtype() {
        return $this->belongsTo(MaterialType::class , 'type_id', 'material_type_id');
    }
    public function warehouse() {
        return $this->belongsTo(\App\Warehouse::class, 'warehouse_id', 'id');
    }
}
