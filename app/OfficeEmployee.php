<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class OfficeEmployee extends Model
{
   
    protected $guarded = [];
    public function department()
    {
        return $this->belongsTo(EmployeeDepartment::class);
    }
    public function employee_salary()
    {
        return $this->hasMany(EmployeeSalary::class,'employee_id','id');
    }
    public function payment(){
        return $this->hasMany(EmployeePayment::class,'employee_id','id');
    }
}
