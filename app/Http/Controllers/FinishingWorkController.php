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
    protected $inventoryManager;

    public function __construct(AccountingService $accountingService, \App\Services\InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
        
        $this->middleware('permission:view_finishing_centers')->only(['index', 'search_finish_number', 'search_from_finish_number', 'search', 'search_non', 'show']);
        $this->middleware('permission:create_finishing_work')->only(['saving_the_work', 'store']);
        $this->middleware('permission:edit_finishing_work')->only(['edit', 'update', 'destroy']);
        $this->middleware('permission:re_saving_the_work')->only(['re_saving_the_work', 'store_refinish']);
        $this->middleware('permission:view_refinish_requests')->only(['request_list']);
        $this->middleware('permission:approve_refinish_requests')->only(['approve_request']);
        $this->middleware('permission:reject_refinish_requests')->only(['delete_request']);
        $this->middleware('permission:return_carpet_from_finishing')->only(['return_to_wash', 'return_to_center']);
    }

    public function saving_the_work(Carpet $carpet, Request $request)
    {
        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first() ?? $carpet;
        
        $selected_team_id = $request->query('team_id') ?: $carpet->finishing_id;
        $effective_team_id = $selected_team_id;

        $openBatches = \App\ProductionBatch::where('type', 'finish')
            ->where('status', 'open')
            ->where(function($q) use ($effective_team_id) {
                if ($effective_team_id) {
                    $q->where('team_id', $effective_team_id);
                }
            })
            ->get();

        $done = FinishingWork::where('carpetId', $carpet->carpet_id)->pluck('category_id')->toArray();
        $teams = FinishingTeam::all();
        $team_categories = FinishingTeamCategory::whereNotIn('id', $done)->get();
        
        $qaitan_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 1)->first();
        $rofo_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 2)->first();
        $cheet_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 3)->first();
        $labaki_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 4)->first();
        $popak_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 5)->first();
        $kash_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 6)->first();
        $rang_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 7)->first();
        $shiraza_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 8)->first();
        $quality_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 9)->first();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'FINISHING_CREDIT')->first();
        
        $currencies = \App\Currency::all();
        $currencyObj = \App\Currency::where('code', 'AFN')->first();
        $currency = ($currencyObj && $currencyObj->exchange_rate > 0) ? (1 / $currencyObj->exchange_rate) : 70.0;

        $warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        return view('finishing-center.create', compact(
            'carpet', 'teams', 'newCarpet', 'openBatches', 'team_categories', 
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currency', 'currencies',
            'qaitan_check', 'rofo_check', 'cheet_check', 'labaki_check', 
            'popak_check', 'kash_check', 'rang_check', 'shiraza_check', 'quality_check',
            'warehouses', 'selected_team_id'
        ));
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

            // ERP Integration: Atomic Inventory Audit + Accounting
            $category = FinishingTeamCategory::find($work->category_id);
            $this->inventoryManager->recordProductionService($work, $carpet, [
                'type' => 'FINISHING',
                'amount' => ($work->currency_code && $work->currency_code !== 'USD') ? $work->price_af : $work->price,
                'currency_code' => $work->currency_code ?? 'USD',
                'exchange_rate' => $work->exchange_rate ?? 1.0,
                'date' => $work->date,
                'party_type' => 'App\FinishingTeam',
                'party_id' => $work->team_id,
                'reference' => $work->finish_number,
                'description' => "هزینه " . ($category->category ?? 'Preparation') . " قالین نمبر " . $carpet->carpet_no,
                'warehouse_id' => $carpet->warehouse_id ?? 1,
                'override_debit_account_id' => $work->debit_account_id,
                'override_credit_account_id' => $work->credit_account_id,
            ]);

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
        return response()->json(['status' => 'error']);
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
        return DB::transaction(function () use ($carpet_id) {
            $carpet = Carpet::find($carpet_id);
            
            // Get original source warehouse from the latest active Finishing Transfer OUT transaction for this carpet
            $originalTransaction = DB::table('inventory_transactions')
                ->where('reference_type', get_class($carpet))
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Finishing Transfer')
                ->where('direction', 'OUT')
                ->where('status', 1)
                ->orderByDesc('id')
                ->first();
                
            $originalWarehouseId = $originalTransaction ? $originalTransaction->warehouse_id : ($carpet->warehouse_id ?? 1);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " از بخش تیاری به دفتر مرکزی بازگشت داده شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $carpet->status = 1;
            $carpet->finishing_id = null;
            $carpet->warehouse_id = $originalWarehouseId;
            $carpet->update();

            // Reverse physical transfer using the new safe transactionType parameter
            $inventoryService = app(\App\Services\InventoryService::class);
            $inventoryService->reverseMovement($carpet, 'Returned from Finishing', 'Finishing Transfer');

            return back()->with('status', 'قالین موفقانه به دفتر مرکزی بازگشت داده شد');
        });
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
                $query->where('carpet_no', 'like', '%' . $search_finish . '%')
                    ->orWhere('map_number', 'like', '%' . $search_finish . '%');
            })->orWhereHas('team', function ($query) use ($search_finish) {
                $query->where('name', 'like', '%' . $search_finish . '%');
            })->orWhereHas('category', function ($query) use ($search_finish) {
                $query->where('category', 'like', '%' . $search_finish . '%');
            })->paginate(100);

        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)->orderBy('updated_at', 'DESC')->paginate(20);
        $team = FinishingTeam::all();
        $check = 'not_null';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check', 'search_finish'));
    }

    public function search_non(Request $request)
    {
        $search_non = $request->search_non;
        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)
            ->where(function ($query) use ($search_non) {
                $query->where('carpet_no', 'like', '%' . $search_non . '%')
                    ->orWhere('map_number', 'like', '%' . $search_non . '%');
            })
            ->orWhereHas('type', function ($query) use ($search_non) {
                $query->where('carpet_type', 'like', '%' . $search_non . '%');
            })->orderBy('updated_at', 'DESC')->paginate(20);
        $finisheds = FinishingWork::paginate(20);
        $team = FinishingTeam::all();
        $check = '';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check', 'search_non'));
    }



    public function re_saving_the_work(Carpet $carpet, Request $request)
    {
        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first() ?? $carpet;
        
        $openBatches = \App\ProductionBatch::where('type', 'finish')->where('status', 'open')->get();

        $teams = FinishingTeam::all();
        $team_categories = FinishingTeamCategory::all();
        $selected_team_id = $request->query('team_id');
        
        $checks = [];
        for($i=1; $i<=9; $i++) {
            $checks[$i] = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', $i)->first();
        }

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'FINISHING_CREDIT')->first();

        $currencies = \App\Currency::all();
        $currencyObj = \App\Currency::where('code', 'AFN')->first();
        $currency = ($currencyObj && $currencyObj->exchange_rate > 0) ? (1 / $currencyObj->exchange_rate) : 70.0;

        return view('finishing-center.re-finish-work', array_merge(compact('carpet', 'teams', 'newCarpet', 'openBatches', 'team_categories', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies', 'currency', 'selected_team_id'), $checks));
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
            
            $currencyCode = $request->currency_code ?? 'USD';
            $exchangeRate = $request->exchange_rate ?? 1.0;
            $unitPrice = $request->input('price_af_' . $field_suffix);
            $totalAmount = 0;
            
            if (in_array($category_id, [1, 3, 5, 6, 7, 9])) { 
                $totalAmount = $newCarpet->area * $unitPrice;
            } elseif (in_array($category_id, [4, 8])) { 
                $totalAmount = $newCarpet->height * $unitPrice * 2;
            } elseif ($category_id == 2) { 
                $totalAmount = $unitPrice;
            }

            // FORENSIC RULE: Calculate Base USD amount using safe division to get correct USD equivalent
            if ($currencyCode == 'USD') {
                $baseUsdAmount = $totalAmount;
            } else {
                $baseUsdAmount = bcdiv((string)$totalAmount, (string)$exchangeRate, 4);
            }

            // price_af is used as a local currency field, storing raw input (PKR, EUR, etc.) without conversion
            $priceAf = $totalAmount;

            $finish->currency_code = $currencyCode;
            $finish->exchange_rate = $exchangeRate;
            $finish->price = $baseUsdAmount;
            $finish->price_af = $priceAf;
            $finish->base_currency_amount = $baseUsdAmount;
            $finish->debit_account_id = $request->override_debit_account_id;
            $finish->credit_account_id = $request->override_credit_account_id;

            if ($request->has('warehouse_id')) {
                $finish->warehouse_id = $request->input('warehouse_id');
            }
            
            if (Auth::user()->isSuperAdmin() || $request->is_direct_store) {
                $finish->status = 1;
                $carpet->total_price += $finish->price;
                $carpet->total_price_af += $finish->price_af;
                $carpet->update();
                $finish->save();

                $category = FinishingTeamCategory::find($finish->category_id);
                $this->inventoryManager->recordProductionService($finish, $carpet, [
                    'type' => 'FINISHING',
                    'amount' => $totalAmount,
                    'currency_code' => $currencyCode,
                    'exchange_rate' => $exchangeRate,
                    'date' => $finish->date,
                    'party_type' => 'App\FinishingTeam',
                    'party_id' => $finish->team_id,
                    'reference' => $finish->finish_number,
                    'description' => "هزینه " . ($category->category ?? 'Preparation') . " قالین نمبر " . $carpet->carpet_no,
                    'warehouse_id' => $carpet->warehouse_id ?? 1,
                    'override_debit_account_id' => $finish->debit_account_id,
                    'override_credit_account_id' => $finish->credit_account_id,
                ]);
            } else {
                $finish->status = 0;
                $finish->save();
            }

            return $finish;
        }
        return null;
    }

    public function store_refinish(Request $request)
    {
        DB::transaction(function () use ($request) {
            $carpet = Carpet::find($request->carpetId);
            $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first() ?? $carpet;

            $categories = [
                1 => 'qaitan', 2 => 'rofo', 3 => 'cheet', 4 => 'labaki',
                5 => 'popak', 6 => 'kash', 7 => 'rang', 8 => 'shiraza', 9 => 'quality'
            ];

            foreach ($categories as $id => $suffix) {
                $this->processWorkCategory($request, $carpet, $newCarpet, $id, $suffix);
            }
        });

        return redirect('/dashboard/finishing-center')->with('status', 'تیاری مجدد با موفقیت ثبت شد');
    }

    public function store(Request $request)
    {
        $request->merge(['is_direct_store' => true]);
        
        DB::transaction(function () use ($request) {
            $carpet = Carpet::where('carpet_id', $request->carpetId)->lockForUpdate()->firstOrFail();
            $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first() ?? $carpet;

            $categories = [
                1 => 'qaitan', 2 => 'rofo', 3 => 'cheet', 4 => 'labaki',
                5 => 'popak', 6 => 'kash', 7 => 'rang', 8 => 'shiraza', 9 => 'quality'
            ];

            $lastSavedFinish = null;
            foreach ($categories as $id => $suffix) {
                $res = $this->processWorkCategory($request, $carpet, $newCarpet, $id, $suffix);
                if ($res) {
                    $lastSavedFinish = $res;
                }
            }

            if ($request->finished == 1) {
                $carpet->status = 5;
            }

            $warehouseId = $request->input('warehouse_id');
            $sourceWarehouseId = $carpet->warehouse_id;

            if ($warehouseId && $warehouseId != $sourceWarehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $inventoryService = app(\App\Services\InventoryService::class);
                $refModel = $lastSavedFinish ?? $carpet;

                // Record Transfer OUT from old warehouse
                $inventoryService->recordMovement([
                    'item_model' => $refModel,
                    'parent_item_model' => $carpet,
                    'type' => 'Finishing Completion Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $sourceWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                // Record Transfer IN to selected target warehouse
                $inventoryService->recordMovement([
                    'item_model' => $refModel,
                    'parent_item_model' => $carpet,
                    'type' => 'Finishing Completion Transfer',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'warehouse_id' => $warehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $carpet->warehouse_id = $warehouseId;
            }

            $carpet->update();
        });

        return redirect('/dashboard/finishing-center')->with('status', 'عملیات تیاری با موفقیت ثبت و در سیستم مالی درج گردید');
    }

    public function show($id)
    {
        $finish = FinishingWork::findOrFail($id);
        $newCarpet = CarpetWash::where('carpetId', $finish->carpetId)->first() ?? $finish->carpet;
        return view('finishing-center.show', compact('finish', 'newCarpet'));
    }

    public function edit($id)
    {
        $finish = FinishingWork::findOrFail($id);
        $carpet = Carpet::where('carpet_id', '=', $finish->carpetId)->first();
        $newCarpet = CarpetWash::where('carpetId', $finish->carpetId)->first() ?? $carpet;
        $teams = FinishingTeam::all();
        $team = FinishingTeam::find($finish->team_id);
        $category = FinishingTeamCategory::find($finish->category_id);
        $team_categories = FinishingTeamCategory::all();

        $currency = $finish->currency_code ?? 'USD';
        
        // FORENSIC RULE: Use price_af to retrieve the original raw local currency (PKR, EUR, AFN), fallback to price for USD
        $totalAmountInOriginalCurrency = ($finish->currency_code == 'USD') ? $finish->price : $finish->price_af;
        $unitPrice = 0;
        $category_id = $finish->category_id;
        
        if (in_array($category_id, [1, 3, 5, 6, 7])) {
            $unitPrice = $newCarpet->area > 0 ? ($totalAmountInOriginalCurrency / $newCarpet->area) : 0;
        } elseif (in_array($category_id, [4, 8])) {
            $unitPrice = ($newCarpet->height > 0) ? ($totalAmountInOriginalCurrency / ($newCarpet->height * 2)) : 0;
        } elseif ($category_id == 2) {
            $unitPrice = $totalAmountInOriginalCurrency;
        }

        $mainPrice = $finish->price;
        $mainPrice_af = round($unitPrice, 4);

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'FINISHING_CREDIT')->first();

        $warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        return view('finishing-center.edit', compact(
            'finish', 'carpet', 'newCarpet', 'teams', 'team', 'category', 'team_categories', 
            'currency', 'mainPrice', 'mainPrice_af',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'warehouses'
        ));
    }

    public function update(Request $request, FinishingWork $finish)
    {
        return DB::transaction(function () use ($request, $finish) {
            $carpet = Carpet::find($request->carpetId);
            $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first() ?? $carpet;

            // Accounting Reversal - pass class name to avoid ID collision reversals with other models
            $this->accountingService->reverseTransactionBySource($finish->id, 'Finishing Work Edited', get_class($finish));

            // Reverse Completion Transfer linked to this FinishingWork
            $inventoryService = app(\App\Services\InventoryService::class);
            $inventoryService->reverseMovement($finish, 'Finishing Work Edited');

            // Recalculate price
            $rate = $request->price_af;
            $totalAmount = 0;
            $category_id = $request->category_id;
            
            if (in_array($category_id, [1, 3, 5, 6, 7])) {
                $totalAmount = $newCarpet->area * $rate;
            } elseif (in_array($category_id, [4, 8])) {
                $totalAmount = $newCarpet->height * $rate * 2;
            } elseif ($category_id == 2) {
                $totalAmount = $rate;
            }

            $currencyCode = $finish->currency_code ?? 'USD';
            $exchangeRate = $finish->exchange_rate ?? 1.0;

            // FORENSIC RULE: Use safe BCMath division to get correct USD equivalent
            $newPriceUsd = ($currencyCode == 'USD') ? $totalAmount : bcdiv((string)$totalAmount, (string)$exchangeRate, 4);
            // price_af is used as a local currency field, storing raw input (PKR, EUR, etc.) without conversion
            $newPriceAfn = $totalAmount;

            // Update Carpet total (subtract old, add new)
            $carpet->total_price = $carpet->total_price - $finish->price + $newPriceUsd;
            $carpet->total_price_af = $carpet->total_price_af - $finish->price_af + $newPriceAfn;
            $carpet->update();

            $finish->finish_number = $request->finish_number;
            $finish->team_id = $request->team_id;
            $finish->category_id = $category_id;
            
            $finish->price = $newPriceUsd;
            $finish->price_af = $newPriceAfn;
            $finish->base_currency_amount = $newPriceUsd;
            
            $finish->debit_account_id = $request->override_debit_account_id;
            $finish->credit_account_id = $request->override_credit_account_id;

            if ($request->has('warehouse_id')) {
                $finish->warehouse_id = $request->input('warehouse_id');
            } else {
                $finish->warehouse_id = null;
            }

            $finish->date = $request->date;
            $finish->description = $request->description;
            $finish->update();

            // ERP Integration: Re-post value addition
            $category = FinishingTeamCategory::find($finish->category_id);
            $this->inventoryManager->recordProductionService($finish, $carpet, [
                'type' => 'FINISHING',
                'mapping_key' => 'FINISHING_CREDIT',
                'amount' => ($finish->currency_code && $finish->currency_code !== 'USD') ? $finish->price_af : $finish->price,
                'currency_code' => $finish->currency_code ?? 'USD',
                'exchange_rate' => $finish->exchange_rate ?? 1.0,
                'date' => $finish->date,
                'party_type' => 'App\FinishingTeam',
                'party_id' => $finish->team_id,
                'reference' => $finish->finish_number,
                'description' => "ویرایش هزینه " . ($category->category ?? 'Preparation') . " قالین نمبر " . $carpet->carpet_no,
                'warehouse_id' => $carpet->warehouse_id ?? 1,
                'override_debit_account_id' => $finish->debit_account_id,
                'override_credit_account_id' => $finish->credit_account_id,
            ]);

            // Re-post completion transfer if finished
            if ($request->finished == 1) {
                $carpet->status = 5;
            } else {
                if ($carpet->status == 5) {
                    $carpet->status = 4;
                }
            }

            $warehouseId = $request->input('warehouse_id');
            $sourceWarehouseId = $carpet->warehouse_id;

            if ($warehouseId && $warehouseId != $sourceWarehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                // Record Transfer OUT from old warehouse
                $inventoryService->recordMovement([
                    'item_model' => $finish,
                    'parent_item_model' => $carpet,
                    'type' => 'Finishing Completion Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $sourceWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                // Record Transfer IN to selected target warehouse
                $inventoryService->recordMovement([
                    'item_model' => $finish,
                    'parent_item_model' => $carpet,
                    'type' => 'Finishing Completion Transfer',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'warehouse_id' => $warehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $carpet->warehouse_id = $warehouseId;
            }
            $carpet->update();

            return redirect('/dashboard/finishing-center')->with('status', 'تیاری با موفقیت ویرایش و سیستم مالی بروزرسانی شد');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $work = FinishingWork::find($id);
            if ($work) {
                $carpetToUpdate = Carpet::find($work->carpetId);
                if ($carpetToUpdate) {
                    $carpetToUpdate->total_price -= $work->price;
                    $carpetToUpdate->total_price_af -= $work->price_af;
                    $carpetToUpdate->update();
                }
            }
            
            // Reverse Accounting - pass class name to avoid ID collision reversals with other models
            $this->accountingService->reverseTransactionBySource($work->id, 'Finishing Work Deleted', get_class($work));
            
            // Revert Carpet completion status and warehouse if this work completed it
            $outTx = DB::table('inventory_transactions')
                ->where('reference_type', get_class($work))
                ->where('reference_id', $work->id)
                ->where('type', 'Finishing Completion Transfer')
                ->where('direction', 'OUT')
                ->where('status', 1)
                ->first();
            
            if ($outTx) {
                $carpet = Carpet::find($work->carpetId);
                if ($carpet) {
                    $carpet->status = 4; // Back to finishing WIP status
                    $carpet->warehouse_id = $outTx->warehouse_id; // Restore source warehouse
                    $carpet->update();
                }
            }

            // Reverse Completion Transfer linked to this FinishingWork
            $inventoryService = app(\App\Services\InventoryService::class);
            $inventoryService->reverseMovement($work, 'Finishing Work Deleted');

            $work->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
