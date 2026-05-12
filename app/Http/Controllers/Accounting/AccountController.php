<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Services\AccountSelectionService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $selectionService;

    public function __construct(AccountSelectionService $selectionService)
    {
        $this->selectionService = $selectionService;
    }

    /**
     * API Endpoint to fetch allowed accounts for a mapping key
     */
    public function getAllowedAccounts(Request $request)
    {
        $mappingKey = $request->mapping_key;
        $side = $request->side; // 'debit' or 'credit'

        if (!$mappingKey || !$side) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $accounts = $this->selectionService->getValidAccounts($mappingKey, $side);

        return response()->json($accounts);
    }
}
