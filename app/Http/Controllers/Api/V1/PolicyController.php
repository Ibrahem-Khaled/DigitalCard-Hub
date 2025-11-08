<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Models\Setting;
use Illuminate\Http\Request;

class PolicyController extends BaseController
{
    /**
     * Get all policies
     */
    public function index(Request $request)
    {
        $policies = Setting::where('group', 'legal')
            ->where('is_public', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'name' => $setting->name,
                    'content' => $setting->value,
                    'updated_at' => $setting->updated_at->toIso8601String(),
                ];
            });

        return $this->successResponse($policies);
    }

    /**
     * Get specific policy by key
     */
    public function show(Request $request, $key)
    {
        $policy = Setting::where('group', 'legal')
            ->where('key', $key)
            ->where('is_public', true)
            ->first();

        if (!$policy) {
            return $this->notFoundResponse('السياسة غير موجودة');
        }

        return $this->successResponse([
            'key' => $policy->key,
            'name' => $policy->name,
            'content' => $policy->value,
            'updated_at' => $policy->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Get privacy policy
     */
    public function privacy()
    {
        $policy = Setting::where('group', 'legal')
            ->where('key', 'privacy_policy')
            ->where('is_public', true)
            ->first();

        if (!$policy) {
            return $this->notFoundResponse('سياسة الخصوصية غير موجودة');
        }

        return $this->successResponse([
            'key' => $policy->key,
            'name' => $policy->name,
            'content' => $policy->value,
            'updated_at' => $policy->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Get terms of service
     */
    public function terms()
    {
        $policy = Setting::where('group', 'legal')
            ->where('key', 'terms_of_service')
            ->where('is_public', true)
            ->first();

        if (!$policy) {
            return $this->notFoundResponse('شروط الاستخدام غير موجودة');
        }

        return $this->successResponse([
            'key' => $policy->key,
            'name' => $policy->name,
            'content' => $policy->value,
            'updated_at' => $policy->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Get refund policy
     */
    public function refund()
    {
        $policy = Setting::where('group', 'legal')
            ->where('key', 'refund_policy')
            ->where('is_public', true)
            ->first();

        if (!$policy) {
            return $this->notFoundResponse('سياسة الاسترداد غير موجودة');
        }

        return $this->successResponse([
            'key' => $policy->key,
            'name' => $policy->name,
            'content' => $policy->value,
            'updated_at' => $policy->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Get shipping policy
     */
    public function shipping()
    {
        $policy = Setting::where('group', 'legal')
            ->where('key', 'shipping_policy')
            ->where('is_public', true)
            ->first();

        if (!$policy) {
            return $this->notFoundResponse('سياسة الشحن غير موجودة');
        }

        return $this->successResponse([
            'key' => $policy->key,
            'name' => $policy->name,
            'content' => $policy->value,
            'updated_at' => $policy->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Get return policy
     */
    public function returns()
    {
        $policy = Setting::where('group', 'legal')
            ->where('key', 'return_policy')
            ->where('is_public', true)
            ->first();

        if (!$policy) {
            return $this->notFoundResponse('سياسة الإرجاع غير موجودة');
        }

        return $this->successResponse([
            'key' => $policy->key,
            'name' => $policy->name,
            'content' => $policy->value,
            'updated_at' => $policy->updated_at->toIso8601String(),
        ]);
    }
}

