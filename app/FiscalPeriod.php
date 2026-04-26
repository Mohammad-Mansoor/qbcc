<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FiscalPeriod extends Model
{
    protected $fillable = ['period_name', 'start_date', 'end_date', 'is_closed'];
}
