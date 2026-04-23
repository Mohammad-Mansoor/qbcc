<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class OfficeCashBook extends Model
{
  
    public function employee_debit()
    {
        return $this->belongsTo(OfficeDebit::class);
    }
}
