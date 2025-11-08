<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\UserResource;
use App\Http\Resources\Api\OrderResource;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    /**
     * Get user profile
     */
    public function index(Request $request)
    {
        return $this->successResponse([
            'user' => new UserResource($request->user()->load('roles')),
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|unique:users,phone,' . $user->id,
            'birth_date' => 'sometimes|nullable|date|before:today',
            'gender' => 'sometimes|nullable|in:male,female',
            'address' => 'sometimes|nullable|string|max:500',
            'city' => 'sometimes|nullable|string|max:255',
            'country' => 'sometimes|nullable|string|max:255',
            'postal_code' => 'sometimes|nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone',
            'birth_date', 'gender', 'address', 'city', 'country', 'postal_code'
        ]));

        return $this->successResponse([
            'user' => new UserResource($user->fresh()->load('roles')),
        ], 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Get user orders
     */
    public function orders(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * Get user loyalty points
     */
    public function loyaltyPoints(Request $request)
    {
        $loyaltyPoints = $request->user()->loyaltyPoints()
            ->where('expires_at', '>', now())
            ->sum('points');

        $transactions = $request->user()->loyaltyPointTransactions()
            ->latest()
            ->paginate(15);

        return $this->successResponse([
            'total_points' => $loyaltyPoints,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Get user referrals
     */
    public function referrals(Request $request)
    {
        $referrals = $request->user()->referrals()
            ->with('referred')
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($referrals, \App\Http\Resources\Api\ReferralResource::class);
    }
}


