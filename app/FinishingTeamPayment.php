<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class FinishingTeamPayment extends Model 
{ 
    protected $guarded = [];
    public function team(){
        return $this->belongsTo(FinishingTeam::class,'team_id','id');
    }

    public function allocations()
    {
        return $this->hasMany(FinishingPaymentAllocation::class, 'finishing_team_payment_id');
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
