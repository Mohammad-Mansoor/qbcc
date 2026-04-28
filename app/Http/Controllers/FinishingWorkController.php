<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Agents;
use App\CarpetWash;
use App\FinishingTeam;
use App\FinishingTeamCategory;
use App\FinishingTotalAccount;
use App\FinishingWork;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinishingWorkController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postFinishingToAccounting($work)
    {
        try {
            $carpet = Carpet::find($work->carpetId);
            $category = FinishingTeamCategory::find($work->category_id);
            $team = FinishingTeam::find($work->team_id);

            $this->accountingService->postAutoTransaction('finishing', 'credit', [
                'date' => $work->date,
                'amount' => $work->price_af,
                'party_type' => 'App\FinishingTeam',
                'party_id' => $work->team_id,
                'reference' => $work->finish_number,
                'description' => "هزینه " . ($category->category ?? 'Preparation') . " قالین نمبر " . ($carpet->carpet_no ?? 'N/A') . " توسط " . ($team->name ?? 'Team'),
                'source_id' => $work->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Finishing Work #" . $work->id . ": " . $e->getMessage());
        }
    }

    public function request_list()
    {
        $requests = FinishingWork::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('finishing-center.refinish-request-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $work = FinishingWork::find($id);
            $work->status = 1;
            $work->update();

            $carpet = Carpet::where('carpet_id', '=', $work->carpetId)->first();
            $carpet->total_price = $carpet->total_price + $work->price;
            $carpet->total_price_af = $carpet->total_price_af + $work->price_af;
            $carpet->update();

            // Accounting Posting
            $this->postFinishingToAccounting($work);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $category = FinishingTeamCategory::find($work->category_id);
            $activity->description = " تایید عملیات " . ($category->category ?? 'Preparation') . " قالین نمبر " . $carpet->carpet_no;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $work = FinishingWork::find($id);
        $work->delete();
        return response()->json(['status', 'error']);
    }


    public function return_to_wash($carpet_id)
    {
        $carpet = Carpet::find($carpet_id);
        $carpet->status = 13;
        $carpet->update();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $carpet->carpet_no . " از بخش تیاری به شست بازگشت داده شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return back()->with('status', 'قالین موفقانه به بخش شست بازگشت داده شد');
    }

    public function return_to_center($carpet_id)
    {
        $carpet = Carpet::find($carpet_id);
        $carpet->status = 1;
        $carpet->update();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $carpet->carpet_no . " از بخش تیاری به دفتر مرکزی بازگشت داده شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return back()->with('status', 'قالین موفقانه به دفتر مرکزی بازگشت داده شد');
    }

    public function index()
    {
        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)->orderBy('updated_at', 'DESC')->paginate(100);
        $finisheds = FinishingWork::orderBy('created_at', 'DESC')->paginate(100);
        $team = FinishingTeam::all();
        $check = '';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check'));
    }

    public function search_finish_number($finish_number, $team_id)
    {
        $team = FinishingTeam::find($team_id);
        $finishing_works = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->distinct()->get(['carpetId']);
        $quantity = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->count();
        $finish_numbers = FinishingWork::where('team_id', $team_id)->distinct()->get(['finish_number']);
        return view('finishing-center.finish-number-list', compact('finishing_works', 'team', 'finish_number', 'quantity', 'finish_numbers'));
    }

    public function search_from_finish_number(Request $request)
    {
        $team_id = $request->team_id;
        $finish_number = $request->finish_number;
        $team = FinishingTeam::find($team_id);
        $finishing_works = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->distinct()->get(['carpetId']);
        $quantity = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->count();
        $finish_numbers = FinishingWork::where('team_id', $team_id)->distinct()->get(['finish_number']);
        return view('finishing-center.finish-number-list', compact('finishing_works', 'team', 'finish_number', 'quantity', 'finish_numbers'));
    }

    public function search(Request $request)
    {
        $search_finish = $request->search_finish;
        $finisheds = FinishingWork::where('finish_number', 'like', '%' . $search_finish . '%')
            ->orWhere('date', 'like', '%' . $search_finish . '%')
            ->orWhereHas('carpet', function ($query) use ($search_finish) {
                $query->where('carpet_no', 'like', '%' . $search_finish . '%');
            })->orWhereHas('team', function ($query) use ($search_finish) {
                $query->where('name', 'like', '%' . $search_finish . '%');
            })->orWhereHas('category', function ($query) use ($search_finish) {
                $query->where('category', 'like', '%' . $search_finish . '%');
            })->paginate(100);

        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)->orderBy('updated_at', 'DESC')->paginate(20);
        $team = FinishingTeam::all();
        $check = 'not_null';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check'));
    }

    public function search_non(Request $request)
    {
        $search_non = $request->search_non;
        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)
            ->where('carpet_no', 'like', '%' . $search_non . '%')
            ->orWhereHas('type', function ($query) use ($search_non) {
                $query->where('carpet_type', 'like', '%' . $search_non . '%');
            })->orderBy('updated_at', 'DESC')->paginate(20);
        $finisheds = FinishingWork::paginate(20);
        $team = FinishingTeam::all();
        $check = '';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check'));
    }

    public function saving_the_work(Carpet $carpet)
    {
        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first() ?? $carpet;
        $lastId = FinishingWork::latest()->first();
        $FinishNo = $lastId ? 'TA-' . (substr($lastId->finish_number, -1) + 1) : 'TA-1';

        $done = FinishingWork::where('carpetId', $carpet->carpet_id)->pluck('category_id')->toArray();
        $teams = FinishingTeam::all();
        $team_categories = FinishingTeamCategory::whereNotIn('id', $done)->get();
        
        $checks = [];
        for($i=1; $i<=8; $i++) {
            $checks[$i] = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', $i)->first();
        }

        return view('finishing-center.create', array_merge(compact('carpet', 'teams', 'newCarpet', 'FinishNo', 'team_categories'), $checks));
    }

    public function re_saving_the_work(Carpet $carpet)
    {
        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first() ?? $carpet;
        $lastId = FinishingWork::latest()->first();
        $FinishNo = $lastId ? 'TA-' . (substr($lastId->finish_number, -1) + 1) : 'TA-1';

        $teams = FinishingTeam::all();
        $team_categories = FinishingTeamCategory::all();
        
        $checks = [];
        for($i=1; $i<=8; $i++) {
            $checks[$i] = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', $i)->first();
        }

        return view('finishing-center.re-finish-work', array_merge(compact('carpet', 'teams', 'newCarpet', 'FinishNo', 'team_categories'), $checks));
    }

    private function processWorkCategory($request, $carpet, $newCarpet, $category_id, $field_suffix)
    {
        if ($request->has($field_suffix . '_checkbox') && $request->input($field_suffix . '_checkbox') == 'on') {
            $finish = new FinishingWork();
            $finish->finish_number = $request->input('finish_number_' . $field_suffix);
            $finish->carpetId = $request->carpetId;
            $finish->team_id = $request->input('team_id_' . $field_suffix);
            $finish->category_id = $category_id;
            $finish->date = $request->input('date_' . $field_suffix);
            $finish->description = $request->description ?? 'Finishing Work';
            
            $rate = $request->input('price_af_' . $field_suffix);
            $price = 0;
            
            // Special calculation logic based on category
            if (in_array($category_id, [1, 3, 5, 6, 7])) { // Area based
                $price = $newCarpet->area * $rate;
            } elseif (in_array($category_id, [4, 8])) { // Height based * 2
                $price = $newCarpet->height * $rate * 2;
            } elseif ($category_id == 2) { // Fixed price
                $price = $rate;
            }

            $finish->price = $price;
            $finish->price_af = $price;
            
            if (Auth::user()->role == 'SP' || $request->is_direct_store) {
                $finish->status = 1;
                $carpet->total_price += $price;
                $carpet->total_price_af += $price;
                $carpet->update();
                $finish->save();
                $this->postFinishingToAccounting($finish);
            } else {
                $finish->status = 0;
                $finish->save();
            }
        }
    }

    public function store_refinish(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $carpet = Carpet::find($request->carpetId);
            $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first() ?? $carpet;

            $categories = [
                1 => 'qaitan', 2 => 'rofo', 3 => 'cheet', 4 => 'labaki',
                5 => 'popak', 6 => 'kash', 7 => 'rang', 8 => 'shiraza'
            ];

            foreach ($categories as $id => $suffix) {
                $this->processWorkCategory($request, $carpet, $newCarpet, $id, $suffix);
            }

            return redirect('/dashboard/finishing-center')->with('status', 'تیاری مجدد با موفقیت ثبت شد');
        });
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->merge(['is_direct_store' => true]);
            $carpet = Carpet::find($request->carpetId);
            $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first() ?? $carpet;

            $categories = [
                1 => 'qaitan', 2 => 'rofo', 3 => 'cheet', 4 => 'labaki',
                5 => 'popak', 6 => 'kash', 7 => 'rang', 8 => 'shiraza'
            ];

            foreach ($categories as $id => $suffix) {
                $this->processWorkCategory($request, $carpet, $newCarpet, $id, $suffix);
            }

            if ($request->finished == 1) {
                $carpet->status = 5;
                $carpet->update();
            }

            return redirect('/dashboard/finishing-center')->with('status', 'عملیات تیاری با موفقیت ثبت و در سیستم مالی درج گردید');
        });
    }

    public function update(Request $request, FinishingWork $finish)
    {
        return DB::transaction(function () use ($request, $finish) {
            $carpet = Carpet::find($request->carpetId);
            $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first() ?? $carpet;

            // Accounting Reversal
            $this->accountingService->reverseTransactionBySource($finish->id, 'Finishing Work Edited');

            // Recalculate price
            $rate = $request->price_af;
            $price = 0;
            $category_id = $request->category_id;
            
            if (in_array($category_id, [1, 3, 5, 6, 7])) {
                $price = $newCarpet->area * $rate;
            } elseif (in_array($category_id, [4, 8])) {
                $price = $newCarpet->height * $rate * 2;
            } elseif ($category_id == 2) {
                $price = $rate;
            }

            // Update Carpet total (subtract old, add new)
            $carpet->total_price = $carpet->total_price - $finish->price + $price;
            $carpet->total_price_af = $carpet->total_price_af - $finish->price_af + $price;
            $carpet->update();

            $finish->finish_number = $request->finish_number;
            $finish->team_id = $request->team_id;
            $finish->category_id = $category_id;
            $finish->price = $price;
            $finish->price_af = $price;
            $finish->date = $request->date;
            $finish->description = $request->description;
            $finish->update();

            // Re-post to accounting
            $this->postFinishingToAccounting($finish);

            return redirect('/dashboard/finishing-center')->with('status', 'تیاری با موفقیت ویرایش و سیستم مالی بروزرسانی شد');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $work = FinishingWork::find($id);
            $this->accountingService->reverseTransactionBySource($work->id, 'Finishing Work Deleted');
            $work->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
