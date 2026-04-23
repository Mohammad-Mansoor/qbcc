<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Agents extends Model 
{

    protected $primaryKey = 'agent_id';
    protected $fillable = ['agent_father_name' , 'agent_address' , 'account_type' , 'national_id',
                            'contract_type' , 'contract_date' , 'contract_scan_file' , 'account_no',
                            'account_status' , 'description' , 'user_id' , 'province_id','image'
                        ];

    public function employee()
    {
        return $this->hasMany(AgentEmployee::class,'agent_id','agent_id');
    }

    public function user() {
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }

    public function phone() {
        return $this->hasMany(AgentPhone::class , 'agent_id' , 'agent_id');
    }

    public function province() {
        return $this->belongsTo(Province::class , 'province_id' , 'province_id');
    }

    public function carpet() {
        return $this->hasMany(Carpet::class , 'agent_id' , 'agent_id');
    }
    public function check_book() {
        return $this->hasMany(CarpetCheckBook::class , 'agent_id' , 'agent_id');
    }

    public function payment(){
        return $this->hasMany(AgentPayment::class,'agent_id','agent_id');
    }

    public function material_sale(){
        return $this->hasMany(MaterialSale::class,'agent_id','agent_id');
    }
}
