<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Agents extends Model 
{

    protected $primaryKey = 'agent_id';
    protected $fillable = ['agent_father_name' , 'agent_address' , 'account_type' , 'national_id',
                            'contract_type' , 'contract_date' , 'contract_scan_file' , 'account_no',
                            'account_status' , 'description' , 'user_id' , 'province_id','image', 'note'
                        ];

    public function employee()
    {
        return $this->hasMany(AgentEmployee::class,'agent_id','agent_id');
    }

    public function user() {
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }

    public function phone() {
        return $this->hasMany(AgentPhone::class , 'agent_id' , 'agent_id');
    }

    public function province() {
        return $this->belongsTo(Province::class , 'province_id' , 'province_id');
    }

    public function carpet() {
        return $this->hasMany(Carpet::class , 'agent_id' , 'agent_id');
    }
    public function check_book() {
        return $this->hasMany(CarpetCheckBook::class , 'agent_id' , 'agent_id');
    }
    public function purchase_invoices() {
        return $this->hasMany(PurchaseInvoice::class , 'agent_id' , 'agent_id');
    }

    public function payment(){
        return $this->hasMany(AgentPayment::class,'agent_id','agent_id');
    }

    public function material_sale(){
        return $this->hasMany(MaterialSale::class,'agent_id','agent_id');
    }

    public function sales_invoices() {
        return $this->hasMany(Invoice::class, 'agent_id', 'agent_id');
    }

    public function netBalanceUsd()
    {
        $purchaseBills = $this->purchase_invoices()->with('carpets')->get();
        $totalOwedPurchases = $purchaseBills->sum(function($bill) { return $bill->total_amount; });
        
        $totalPaidPurchases = \DB::table('agent_payment_allocations')
            ->join('purchase_invoices', 'agent_payment_allocations.allocatable_id', '=', 'purchase_invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\PurchaseInvoice')
            ->where('purchase_invoices.agent_id', $this->agent_id)
            ->sum('base_allocated_amount');

        $salesInvoices = $this->sales_invoices()->whereIn('type', ['dye', 'yarn'])->with('material_sales')->get();
        $totalReceivableSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });

        $totalReceivedSales = \DB::table('agent_payment_allocations')
            ->join('invoices', 'agent_payment_allocations.allocatable_id', '=', 'invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\Invoice')
            ->where('invoices.agent_id', $this->agent_id)
            ->sum('base_allocated_amount');

        $totalBaseReceived = $this->payment()->where('status', 1)->doesntHave('allocations')->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = $this->payment()->where('status', 1)->doesntHave('allocations')->where('type', 'گرفت')->sum('base_amount');

        return (($totalOwedPurchases - $totalPaidPurchases) + $totalBaseReceived) - (($totalReceivableSales - $totalReceivedSales) + $totalBaseSent);
    }

    public function netBalanceAfn()
    {
        $totalAfnReceived = $this->payment()->where('status', 1)->doesntHave('allocations')->where('type', 'رسید')->sum('amount_af');
        $totalAfnSent = $this->payment()->where('status', 1)->doesntHave('allocations')->where('type', 'گرفت')->sum('amount_af');
        return $totalAfnReceived - $totalAfnSent;
    }
}
