<?php
/**
 * Tinker Code to Get Admin Verification Codes
 * 
 * Usage:
 * php artisan tinker
 * Then copy and paste the code below
 */

// Get latest verification code for admin user
$adminUser = \App\Models\User::whereHas('roles', function($query) {
    $query->where('slug', 'admin');
})->first();

if ($adminUser) {
    $latestCode = \App\Models\VerificationCode::where('user_id', $adminUser->id)
        ->where('verified', false)
        ->where('expires_at', '>', now())
        ->orderBy('created_at', 'desc')
        ->first();
    
    if ($latestCode) {
        echo "\n=== كود التحقق للادمن ===\n";
        echo "البريد: " . $adminUser->email . "\n";
        echo "الكود: " . $latestCode->code . "\n";
        echo "النوع: " . ($latestCode->type === 'registration' ? 'تسجيل' : 'دخول') . "\n";
        echo "تاريخ الإنشاء: " . $latestCode->created_at->format('Y-m-d H:i:s') . "\n";
        echo "ينتهي في: " . $latestCode->expires_at->format('Y-m-d H:i:s') . "\n";
        echo "المتبقي: " . now()->diffInMinutes($latestCode->expires_at) . " دقيقة\n";
        echo "==========================\n\n";
    } else {
        echo "لا توجد أكواد تحقق نشطة للادمن\n";
    }
} else {
    echo "لم يتم العثور على مستخدم ادمن\n";
}

// Alternative: Get all admin verification codes
// \App\Models\VerificationCode::whereHas('user.roles', function($query) {
//     $query->where('slug', 'admin');
// })->where('verified', false)->where('expires_at', '>', now())->orderBy('created_at', 'desc')->get()->each(function($code) {
//     echo $code->user->email . " => " . $code->code . " (ينتهي في " . $code->expires_at->diffForHumans() . ")\n";
// });

