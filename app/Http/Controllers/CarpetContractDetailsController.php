<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Carpet;
use App\CarpetType;
use App\CarpetOrder;
use App\Agents;

class CarpetContractDetailsController extends Controller
{
    public function carpet_contract_details(Carpet $carpet){
        return view('carpets.carpet-contract-details',compact('carpet'));
    }
}
