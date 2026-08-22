<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePayment extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(OfficeEmployee::class, 'employee_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(\App\Currency::class, 'currency_id', 'id');
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
