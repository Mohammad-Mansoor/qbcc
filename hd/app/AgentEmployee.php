<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class AgentEmployee extends Model  
{

    protected $guarded = [];
    
    public function agent()
    {
        return $this->belongsTo(Agents::class,'agent_id','agent_id');
    }
     public function carpet()
    {
        return $this->hasMany(Carpet::class, 'employee_id','id');
    }
}
