<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $primaryKey = 'co_id';
    protected  $guarded = [];
}
