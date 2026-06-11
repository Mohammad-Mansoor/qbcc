<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WashingTeam extends Model
{
   
    protected $guarded = [];


    
    public function carpet_wash(){
        return $this->hasMany(CarpetWash::class,'team_id');
    }
    
    public function carpet(){
        return $this->hasMany(Carpet::class,'washing_id');
    }

    public function payment(){
        return $this->hasMany(WashingPayment::class,'team_id','id');
    }

    public function getNormalizedBalanceAttribute()
    {
        return \DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')
            ->where('party_id', $this->id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('base_credit - base_debit'));
    }

    public function getUsdBalanceAttribute()
    {
        return \DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')
            ->where('party_id', $this->id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('base_credit - base_debit'));
    }

    public function getAfBalanceAttribute()
    {
        return \DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')
            ->where('party_id', $this->id)
            ->where('ledger_entries.currency_code', 'AFN')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('credit - debit'));
    }
}
