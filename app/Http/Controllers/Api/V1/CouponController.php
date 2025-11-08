<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends BaseController
{
    /**
     * Get available coupons
     */
    public function index(Request $request)
    {
        $coupons = Coupon::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', now());
            })
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($coupons);
    }

    /**
     * Validate coupon
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $coupon = Coupon::where('code', $request->input('code'))->first();

        if (!$coupon) {
            return $this->errorResponse('كود الخصم غير صحيح', 400);
        }

        if (!$coupon->isValid()) {
            return $this->errorResponse('كود الخصم غير صالح أو منتهي الصلاحية', 400);
        }

        if ($request->user() && !$coupon->canBeUsedBy($request->user()->id)) {
            return $this->errorResponse('لا يمكنك استخدام هذا الكوبون', 400);
        }

        return $this->successResponse([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'minimum_amount' => $coupon->minimum_amount,
                'maximum_discount' => $coupon->maximum_discount,
            ],
        ], 'الكوبون صالح للاستخدام');
    }
}

