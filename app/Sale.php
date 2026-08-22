<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Sale extends Model 
{
    
    protected $guarded = [];

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function carpet(){
        return $this->belongsTo(Carpet::class,'carpet_id','carpet_id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_credit_account_id');
    }

    public function cogsDebitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_cogs_debit_id');
    }

    public function cogsCreditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_cogs_credit_id');
    }
}
