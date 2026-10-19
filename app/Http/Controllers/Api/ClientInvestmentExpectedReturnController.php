<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentExpectedReturn;
use Illuminate\Http\Request;

class ClientInvestmentExpectedReturnController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'investment');
        $investmentExpectedReturns = InvestmentExpectedReturn::where('status', 1)
            ->where('type', $type)
            ->get();

        return response()->json([
            'status' => 'Success',
            'message' => ucfirst($type) . ' Expected Returns fetched successfully.',
            'data' => $investmentExpectedReturns
        ]);
    }
}
