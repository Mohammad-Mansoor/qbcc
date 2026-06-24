<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AssetsReportController extends Controller
{
    public function index(Request $request)
    {
        return $this->generateReport($request, 'view');
    }

    public function exportExcel(Request $request)
    {
        abort_if(!auth()->user()->can('export_assets_report_excel'), 403, 'شما اجازه دریافت فایل اکسل این گزارش را ندارید.');
        return $this->generateReport($request, 'excel');
    }

    public function exportPdf(Request $request)
    {
        abort_if(!auth()->user()->can('export_assets_report_pdf'), 403, 'شما اجازه دریافت فایل PDF این گزارش را ندارید.');
        return $this->generateReport($request, 'pdf');
    }

    private function generateReport(Request $request, $type)
    {
        $query = DB::table('ajnas_account_details')
            ->join('ajnas_accounts', 'ajnas_account_details.ajnas_account_id', '=', 'ajnas_accounts.aa_id')
            ->leftJoin('currencies', 'ajnas_account_details.currency_id', '=', 'currencies.id')
            ->select('ajnas_account_details.*', 'ajnas_accounts.aa_name', 'currencies.symbol as currency_symbol', 'currencies.code as currency_code');

        // Date Filter
        if ($request->filled('from_date')) {
            $query->whereDate('acquisition_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('acquisition_date', '<=', $request->to_date);
        }

        // Account Filter
        if ($request->filled('account_id')) {
            $query->where('ajnas_account_id', $request->account_id);
        }

        // Asset Class Filter
        if ($request->filled('asset_class')) {
            $query->where('asset_class', $request->asset_class);
        }
        
        // Number Filter
        if ($request->filled('asset_number')) {
            $query->where('asset_number', 'like', '%' . $request->asset_number . '%');
        }

        $accounts = DB::table('ajnas_accounts')->get();
        $classes = DB::table('ajnas_account_details')->select('asset_class')->whereNotNull('asset_class')->where('asset_class', '!=', '')->distinct()->pluck('asset_class');

        // KPIs calculation
        $cloneQuery = clone $query;
        $allRecords = $cloneQuery->get();
        
        $totalItems = $allRecords->count();
        $totalCostUsd = 0;
        $totalBookValueUsd = 0;

        foreach ($allRecords as $record) {
            $rate = $record->exchange_rate ?? 1.0;
            $costUsd = $record->acquisition_cost; 
            $totalCostUsd += $costUsd;

            // Simple Book value approx:
            $salvage = $record->estimated_salvage_value ?? 0;
            $life = $record->estimated_useful_life ?? 1;
            
            // Age in years
            $ageDays = \Carbon\Carbon::parse($record->acquisition_date)->diffInDays(\Carbon\Carbon::now());
            $ageYears = $ageDays / 365.25;
            if ($ageYears > $life) {
                $ageYears = $life;
            }
            if ($ageYears < 0) {
                $ageYears = 0;
            }

            $depreciableUsd = $costUsd - $salvage;
            if ($life > 0) {
                $accumulated = ($depreciableUsd / $life) * $ageYears;
                $bookValue = $costUsd - $accumulated;
            } else {
                $bookValue = $salvage; 
            }
            $totalBookValueUsd += max($salvage, $bookValue);
        }

        $kpis = [
            'total_items' => $totalItems,
            'total_cost_usd' => $totalCostUsd,
            'total_book_value_usd' => $totalBookValueUsd
        ];

        // Execute query
        if ($type === 'view') {
            $assets = $query->orderBy('aad_id', 'desc')->paginate(30);
            $assets->appends($request->all());
            
            return view('assets-accounts.reports.assets_report', [
                'assets' => $assets,
                'kpis' => $kpis,
                'accounts' => $accounts,
                'classes' => $classes,
                'request' => $request
            ]);
        } else {
            $assets = $query->orderBy('aad_id', 'desc')->get();
            $issueDate = Carbon::now()->format('Y-m-d H:i');

            if ($type === 'excel') {
                return view('assets-accounts.reports.assets_report_excel', [
                    'assets' => $assets,
                    'accounts' => $accounts,
                    'classes' => $classes,
                    'issueDate' => $issueDate,
                    'request' => $request
                ]);
            } else {
                $headerPath = public_path('images/header.png');
                $footerPath = public_path('images/footer.png');
                
                $headerBase64 = '';
                if (file_exists($headerPath)) {
                    $headerBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath));
                }

                $footerBase64 = '';
                if (file_exists($footerPath)) {
                    $footerBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath));
                }

                return view('assets-accounts.reports.assets_report_pdf', [
                    'assets' => $assets,
                    'accounts' => $accounts,
                    'classes' => $classes,
                    'issueDate' => $issueDate,
                    'request' => $request,
                    'headerBase64' => $headerBase64,
                    'footerBase64' => $footerBase64
                ]);
            }
        }
    }
}
