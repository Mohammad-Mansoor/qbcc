<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransferItem extends Model
{
    protected $guarded = [];

    public function transfer()
    {
        return $this->belongsTo(WarehouseTransfer::class, 'warehouse_transfer_id');
    }

    public function itemModel()
    {
        return $this->morphTo(null, 'item_type', 'ref_id');
    }

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id', 'material_category_id');
    }
}
