<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Kachaee extends Model 
{
   
    protected $guarded = [];
    public function carpet()
    {
        return $this->hasOne(Carpet::class,'kachee_id','id');
    }
    public function repair(){
        return $this->hasOne(CarpetRepair::class,'team_id','id');
    }

    public function payment(){
        return $this->hasMany(KachaeePayment::class,'team_id','id');
    }

    public function getTotalUsdBalanceAttribute()
    {
        $ledger = \DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')
            ->where('party_id', $this->id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->select(\DB::raw('SUM(base_credit - base_debit) as balance'))
            ->first();
        return (float)($ledger ? $ledger->balance : 0);
    }

    public function getTotalAfBalanceAttribute()
    {
        $ledger = \DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')
            ->where('party_id', $this->id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->where('ledger_entries.currency_code', 'AFN')
            ->select(\DB::raw('SUM(credit - debit) as balance'))
            ->first();
        return (float)($ledger ? $ledger->balance : 0);
    }
}
