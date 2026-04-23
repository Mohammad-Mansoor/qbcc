<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $users = User::where('role','!=','AO')->paginate(30);
        $editUser = '';
        return view('users.user-list',compact('users','editUser'));
    }
    public function search(Request $request){
        $search = $request->search;
        $users = User::where('name','like','%'.$search.'%')->orWhere('last_name','like','%'.$search.'%')
            ->orWhere('email','like','%'.$search.'%')->paginate(30);
        $editUser = '';
        return view('users.user-list',compact('users','editUser'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->role = $request->role;
        $user->email = $request->email;
        $user->password = Hash::make($request->confirm);
        $user->save();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کاربر به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if($user) {
            return redirect('/dashboard/users')->with('status', 'کاربر موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/users')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $users = User::where('role','!=','AO')->paginate(30);
        $editUser = User::find($id);
        return view('users.user-list',compact('users','editUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        
        
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        if($user->role == 'AO'){
            $user->role = 'AO';
        }else{
            $user->role = $request->role;
        }
        $user->email = $request->email;
        if ($user->password != $request->confirm){
            $user->password = Hash::make($request->confirm);
        }
        $user->save();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کاربر به نام " . $request->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if (Auth::user()->id == $user->id){
            return redirect('/logout');
        }else{
            if($user) {
                return redirect('/dashboard/users')->with('status', 'کاربر موفقانه ثبت شد !');
            }
            else{
                return redirect('/dashboard/users')->with('error', 'مشکل در سرور وجود داره!');
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کاربر به نام " . $user->name . " از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $user->delete();
        if($user) {
            return response()->json(['status' => 'success']);
        }
    }
}
