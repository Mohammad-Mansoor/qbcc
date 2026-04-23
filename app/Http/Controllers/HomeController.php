<?php

namespace App\Http\Controllers;

use App\Blog;
use App\BlogCategory;
use App\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

    }
    public function delete_duplicate(){
        /**
         *
         *
         *     DELETE t1 FROM carpets  t1 INNER JOIN carpets t2 WHERE t1.carpet_id > t2.carpet_id AND t1.carpet_no = t2.carpet_no
         */
    }
    public function log_activity(){
        $now = Carbon::now();

        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i');
        $subday =  Carbon::now()->subDays(30)->format('Y-m-d H:i');

        $audit = Audit::where('created_at','<',$subday)->delete();

        $all = Audit::select('audits.id','users.role','users.name','audits.event','audits.auditable_type','audits.new_values','audits.old_values','audits.url','audits.created_at')
            ->join('users','users.id','=','audits.user_id')
            ->whereBetween('audits.created_at',[$weekStartDate,$weekEndDate])->orderBy('audits.id','desc')
            ->get();

        return view ('log-activity',compact('all'));
    }
}
