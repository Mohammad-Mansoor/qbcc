<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MaterialSale extends Model 
{

    protected  $guarded =[];
    public function category() {
        return $this->belongsTo(MaterialCategory::class , 'category_id', 'material_category_id');
    }
    public function type() {
        return $this->belongsTo(MaterialType::class , 'type_id', 'material_type_id');
    }
    public function agent(){
        return $this->belongsTo(Agents::class,'agent_id','agent_id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }

    public function debitAccount(){
        return $this->belongsTo(ChartOfAccount::class,'override_debit_account_id');
    }

    public function creditAccount(){
        return $this->belongsTo(ChartOfAccount::class,'override_credit_account_id');
    }

    public function cogsDebitAccount(){
        return $this->belongsTo(ChartOfAccount::class,'override_cogs_debit_id');
    }

    public function cogsCreditAccount(){
        return $this->belongsTo(ChartOfAccount::class,'override_cogs_credit_id');
    }
}
