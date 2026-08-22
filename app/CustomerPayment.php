<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CustomerPayment extends Model
{

    protected $guarded = [];
    public function agent()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'order_id', 'co_id');
    }

    public function allocations()
    {
        return $this->hasMany(InvoicePayment::class, 'payment_id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_credit_account_id');
    }
}
