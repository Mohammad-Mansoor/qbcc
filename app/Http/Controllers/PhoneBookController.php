<?php

namespace App\Http\Controllers;

use App\PhoneBook;
use Illuminate\Http\Request;

class PhoneBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_phone_book')->only(['index', 'search', 'show']);
        $this->middleware('permission:create_phone_book')->only(['create', 'store']);
        $this->middleware('permission:edit_phone_book')->only(['edit', 'update']);
        $this->middleware('permission:delete_phone_book')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $phone_book = PhoneBook::paginate(20);
        return view('phone-book.phone-book', compact('phone_book'));
    }
    public function search(Request $request)
    {
        $search = $request->search;


        $phone_book = PhoneBook::where('name', 'like','%'.$search.'%')
            ->orWhere('phone', 'like', '%' .$search.'%')
            ->orWhere('job_title', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->paginate(20);

        return view('phone-book.phone-book', compact('phone_book'));


    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('phone-book.create-phone-book');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $phone = new  PhoneBook();
        $phone->name = $request->name;
        $phone->job_title = $request->job_title;
        $phone->phone = $request->phone;
        $phone->email = $request->email;
        $phone->save();
        return redirect('/dashboard/phone-book')->with('status','موفقانه ثبت شد');
        
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
        $phone = PhoneBook::find($id);
        return view('phone-book.edit-phone-book',compact('phone'));
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
        $phone =  PhoneBook::find($id);
        $phone->name = $request->name;
        $phone->job_title = $request->job_title;
        $phone->phone = $request->phone;
        $phone->email = $request->email;
        $phone->save();
        return redirect('/dashboard/phone-book')->with('status','موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $phone = PhoneBook::find($id);
        $phone->delete();
        if($phone) {
            return response()->json(['status' => 'success']);
        }
    }
}
