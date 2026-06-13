<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StringSeller extends Model 
{
  
    protected $guarded = [];
    public function materialStock()
    {
        return $this->hasMany(MaterialStock::class,'seller_id');
    }


    public function purchase() {
        return $this->hasMany(PurchaseMaterial::class , 'seller_id' , 'id');
    }

    public function payment(){
        return $this->hasMany(SellerPayment::class,'seller_id','id');
    }

    public function purchaseBills() {
        return $this->hasMany(\App\RawMaterialPurchaseBill::class, 'seller_id', 'id');
    }
}
