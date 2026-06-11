<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Carpet extends Model 
{
    
    protected $primaryKey = 'carpet_id';
    protected  $guarded = [];
    public function agent() {
        return $this->belongsTo(Agents::class , 'agent_id' , 'agent_id');
    }
    public function carpet_order() {
        return $this->belongsTo(CustomerOrder::class , 'order_id' , 'co_id');
    }
    public function type() {
        return $this->belongsTo(CarpetType::class , 'type_id' , 'carpet_type_id');
    }
    public function check_book() {
        return $this->HasOne(CarpetCheckBook::class , 'carpet_id' , 'carpet_id');
    }
    public function purchase_invoice() {
        return $this->belongsTo(PurchaseInvoice::class , 'purchase_invoice_id' , 'id');
    }
    public function repair() {
        return $this->hasMany(CarpetRepair::class , 'carpetId' , 'carpet_id');
    }
    public function carpetEmployee() {
        return $this->belongsTo(AgentEmployee::class , 'employee_id' , 'id');
    }

    public function carpetMaterial() {
        return $this->hasMany(CarpetMaterial::class , 'carpet_id' , 'carpet_id');
    }
    public function kachaee(){
        return $this->belongsTo(Kachaee::class,'kachaee_id','id');
    }
    public function washing(){
        return $this->belongsTo(WashingTeam::class,'washing_id','id');
    }
    public function finishing_team(){
        return $this->belongsTo(FinishingTeam::class,'finishing_id','id');
    }

    public function carpet_wash(){
        return $this->hasOne(CarpetWash::class,'carpetId','carpet_id');
    }

    public function sale(){
        return $this->HasOne(Sale::class,'carpet_id','carpet_id');
    }
    public function package(){
        return $this->belongsTo(Package::class,'package_id','id');
    }
    public function quality(){
        return $this->belongsTo(Quality::class,'quality_id','id');
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }

}
