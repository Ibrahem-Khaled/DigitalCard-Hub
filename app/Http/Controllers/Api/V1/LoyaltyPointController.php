<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

class LoyaltyPointController extends BaseController
{
    /**
     * Get user loyalty points
     */
    public function index(Request $request)
    {
        $loyaltyPoints = $request->user()->loyaltyPoints()
            ->where('expires_at', '>', now())
            ->sum('points');

        return $this->successResponse([
            'total_points' => $loyaltyPoints,
        ]);
    }

    /**
     * Get loyalty point transactions
     */
    public function transactions(Request $request)
    {
        $transactions = $request->user()->loyaltyPointTransactions()
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($transactions);
    }
}


