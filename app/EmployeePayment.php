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
}
