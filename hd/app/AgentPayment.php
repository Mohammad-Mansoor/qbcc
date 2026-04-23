<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class AgentPayment extends Model
{
 
    protected $guarded = [];
    public function agent(){
        return $this->belongsTo(Agents::class,'agent_id','agent_id');
    }
}
