<?php

namespace App\Http\Controllers\Auth;

use App\Activity;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     public function login(Request $request)
    {   
        $input = $request->all();
   
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
   
        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            if (auth()->user()) {
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = "استفاده کننده بنام ". Auth::user()->name . " وارد سیستم شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

                if (auth()->user()->role == 'AO'){
                    $usr = User::find(auth()->user()->id);
                    if ($usr && $usr->agents) {
                        return redirect('/dashboard/agent-payments/'.$usr->agents->agent_id);
                    }
                }
                
                return redirect(self::getDashboardRouteForUser(auth()->user()));

            }else{
                return redirect()->back()->with('error','Email-Address And Password Are Wrong.');
            }
        }else{
            return redirect()->back()->with('error','Email-Address And Password Are Wrong.');
        }
          
    }

    public static function getDashboardRouteForUser($user)
    {
        if ($user->hasPermissionTo('view_production_dashboard')) return '/dashboard/production';
        if ($user->hasPermissionTo('view_inventory_dashboard')) return '/dashboard/inventory';
        if ($user->hasPermissionTo('view_finance_dashboard')) return '/dashboard/finance';
        if ($user->hasPermissionTo('view_sales_dashboard')) return '/dashboard/sales';
        if ($user->hasPermissionTo('view_purchases_dashboard')) return '/dashboard/purchases';
        if ($user->hasPermissionTo('view_cost_analytics')) return '/dashboard/cost-analytics';
        

        return '/dashboard/profile'; // Fallback
    }
}
