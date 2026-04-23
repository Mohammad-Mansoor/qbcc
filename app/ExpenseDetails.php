<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class ExpenseDetails extends Model
{

    protected  $guarded = [];
    public function debit()
    {
        return $this->belongsTo(OfficeDebit::class);
    }
}
