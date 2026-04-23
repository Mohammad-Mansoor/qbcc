<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class EmployeeDepartment extends Model 
{

    protected $primaryKey = 'id';
    protected $guarded = [];
    public function employee()
    {
        return $this->hasMany(OfficeEmployee::class);
    }
}
