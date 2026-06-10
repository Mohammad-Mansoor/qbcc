<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(Agents::class, 'agent_id', 'agent_id');
    }

    public function carpets()
    {
        return $this->hasMany(Carpet::class, 'purchase_invoice_id', 'id');
    }
}
