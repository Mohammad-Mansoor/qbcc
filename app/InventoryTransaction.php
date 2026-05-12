<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'item_id', 'warehouse_id', 'transaction_type', 'quantity', 
        'unit_cost', 'total_cost', 'date', 'reference', 'description'
    ];
}
