<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\ReferralResource;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReferralController extends BaseController
{
    /**
     * Get user referral code
     */
    public function code(Request $request)
    {
        $user = $request->user();
        
        // Get existing referral code from referrals table
        $referral = Referral::where('referrer_id', $user->id)
            ->where('status', 'active')
            ->first();
        
        if (!$referral) {
            // Generate unique referral code
            do {
                $referralCode = strtoupper(substr(md5($user->id . $user->email . time()), 0, 8));
            } while (Referral::where('referral_code', $referralCode)->exists());
            
            // Create referral record
            $referral = Referral::create([
                'referrer_id' => $user->id,
                'referred_id' => $user->id, // Temporary, will be updated when someone uses it
                'referral_code' => $referralCode,
                'status' => 'active',
            ]);
        }

        return $this->successResponse([
            'referral_code' => $referral->referral_code,
            'referral_link' => url('/register?ref=' . $referral->referral_code),
        ]);
    }

    /**
     * Get user referrals
     */
    public function index(Request $request)
    {
        $referrals = Referral::where('referrer_id', $request->user()->id)
            ->with('referred')
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($referrals, ReferralResource::class);
    }

    /**
     * Get single referral
     */
    public function show(Request $request, $id)
    {
        $referral = Referral::where('id', $id)
            ->where('referrer_id', $request->user()->id)
            ->with('referred')
            ->first();

        if (!$referral) {
            return $this->notFoundResponse('الإحالة غير موجودة');
        }

        return $this->successResponse(new ReferralResource($referral));
    }

    /**
     * Get referral statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        
        $totalReferrals = Referral::where('referrer_id', $user->id)->count();
        $activeReferrals = Referral::where('referrer_id', $user->id)
            ->where('status', 'active')
            ->count();
        $completedReferrals = Referral::where('referrer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $totalCommission = Referral::where('referrer_id', $user->id)
            ->where('status', 'completed')
            ->sum('commission_amount');
        $totalReward = Referral::where('referrer_id', $user->id)
            ->where('status', 'completed')
            ->sum('reward_amount');

        return $this->successResponse([
            'total_referrals' => $totalReferrals,
            'active_referrals' => $activeReferrals,
            'completed_referrals' => $completedReferrals,
            'total_commission' => (float) $totalCommission,
            'total_reward' => (float) $totalReward,
        ]);
    }
}

