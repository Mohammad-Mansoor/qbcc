<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class OfficeDebit extends Model 
{

    protected $guarded = [];
    public function cash_book()
    {
       return $this->hasOne(OfficeCashBook::class);
    }

    public function employee()
    {
       return $this->belongsTo(OfficeEmployee::class);
    }
    public function details()
    {
        return $this->hasMany(ExpenseDetails::class);
    }
}
