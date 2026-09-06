<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Carpet;
use DB;

class GlobalSearchController extends Controller
{
    public function searchCarpets(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $prefix = config('company.carpet_no_prefix', 'QB');
        // Clean query from prefix, prefix with dash, etc. for numerical comparison if possible
        $cleanQuery = preg_replace('/^' . preg_quote($prefix, '/') . '-?/i', '', $query);

        // Search by map_number or carpet_no matching directly or matching the number part
        $carpets = Carpet::where('map_number', 'LIKE', "%{$query}%")
            ->orWhere('carpet_no', 'LIKE', "%{$query}%");
            
        if (!empty($cleanQuery) && is_numeric($cleanQuery)) {
            $carpets->orWhere('carpet_no', 'LIKE', "%{$cleanQuery}%")
                    ->orWhere('carpet_no', 'LIKE', "{$prefix}-{$cleanQuery}")
                    ->orWhere('carpet_no', 'LIKE', "{$prefix}{$cleanQuery}");
        }

        $carpets = $carpets->with(['type', 'quality', 'agent.user'])
            ->limit(10)
            ->get();

        $results = [];
        foreach ($carpets as $carpet) {
            $statusStr = $this->getStatusString($carpet->status);
            $results[] = [
                'id' => $carpet->carpet_id,
                'carpet_no' => $carpet->carpet_no,
                'map_number' => $carpet->map_number ?? 'N/A',
                'type' => $carpet->type ? $carpet->type->carpet_type : 'N/A',
                'quality' => $carpet->quality ? $carpet->quality->quality : 'N/A',
                'agent' => $carpet->agent && $carpet->agent->user ? $carpet->agent->user->name : 'N/A',
                'size' => "{$carpet->width} x {$carpet->height}",
                'area' => $carpet->area,
                'status' => $statusStr,
                'url' => route('global-search.carpet.details', $carpet->carpet_id)
            ];
        }

        return response()->json($results);
    }

    public function showCarpet($id)
    {
        $carpet = Carpet::with([
            'agent.user', 
            'type', 
            'quality', 
            'purchase_invoice', 
            'warehouse', 
            'sale.customer', 
            'repair.team', 
            'carpet_wash.washing_team', 
            'finishing_works.team',
            'carpet_order'
        ])->findOrFail($id);

        $statuses = [
            '' => 'همه حالت‌ها',
            0 => 'در نزد نماینده',
            1 => 'در گدام مرکزی',
            2 => 'کچایی نشده',
            12 => 'کچایی شده',
            3 => 'شست نشده',
            13 => 'شست شده',
            4 => 'بخش تیاری',
            5 => 'آماده فروش',
            6 => 'فروخته شده',
        ];
        
        $carpet->status_string = $statuses[$carpet->status] ?? 'نامشخص';

        return view('global-search.carpet-details', compact('carpet'));
    }

    private function getStatusString($status)
    {
        $statuses = [
            0 => 'در نزد نماینده',
            1 => 'در گدام مرکزی',
            2 => 'کچایی نشده',
            12 => 'کچایی شده',
            3 => 'شست نشده',
            13 => 'شست شده',
            4 => 'بخش تیاری',
            5 => 'آماده فروش',
            6 => 'فروخته شده',
        ];
        
        return $statuses[$status] ?? 'نامشخص';
    }
}
