<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(WarehouseTransferItem::class);
    }

    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }



    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
