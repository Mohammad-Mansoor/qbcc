<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class AgentPayment extends Model
{
 
    protected $guarded = [];
    public function agent(){
        return $this->belongsTo(Agents::class,'agent_id','agent_id');
    }

    public function allocations()
    {
        return $this->hasMany(AgentPaymentAllocation::class, 'agent_payment_id');
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
