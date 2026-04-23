<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class AgentPhone extends Model
{
    
    protected $primaryKey = 'phone_id';

    protected $fillable = ['phone_no' , 'agent_id'];

    public function agents() {
        return $this->belongsTo(Agents::class , 'agent_id' , 'agent_id');
    }
}
