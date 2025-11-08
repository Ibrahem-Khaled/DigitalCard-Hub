<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\LoyaltyPointTransactionResource;
use Illuminate\Http\Request;

class LoyaltyPointController extends BaseController
{
    /**
     * Get user loyalty points
     */
    public function index(Request $request)
    {
        $loyaltyPoints = $request->user()->loyaltyPoints()
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where('is_active', true)
            ->sum('points');

        return $this->successResponse([
            'total_points' => (int) $loyaltyPoints,
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

        return $this->paginatedResponse($transactions, LoyaltyPointTransactionResource::class);
    }
}


