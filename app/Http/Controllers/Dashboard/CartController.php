<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cart::with(['user', 'items.product']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('session_id', 'like', "%{$search}%")
                ->orWhere('coupon_code', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'abandoned') {
                $query->abandoned();
            } elseif ($request->status === 'active') {
                $query->where('is_abandoned', false);
            }
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        // فلترة حسب الفترة
        if ($request->filled('period')) {
            $days = match ($request->period) {
                'day' => 1,
                'week' => 7,
                'month' => 30,
                'quarter' => 90,
                'year' => 365,
                default => 30
            };
            $query->where('created_at', '>=', now()->subDays($days));
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'total_amount', 'last_activity_at', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $carts = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_carts' => Cart::count(),
            'active_carts' => Cart::where('is_abandoned', false)->count(),
            'abandoned_carts' => Cart::abandoned()->count(),
            'total_value' => Cart::sum('total_amount'),
            'abandoned_value' => Cart::abandoned()->sum('total_amount'),
            'avg_cart_value' => Cart::avg('total_amount'),
            'recent_carts' => Cart::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $users = User::select('id', 'first_name', 'last_name', 'email')->get();

        return view('dashboard.carts.index', compact('carts', 'stats', 'users'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        $cart->load(['user', 'items.product', 'coupon']);

        // إحصائيات السلة
        $cartStats = [
            'items_count' => $cart->items_count,
            'subtotal' => $cart->subtotal,
            'discount_amount' => $cart->discount_amount,
            'tax_amount' => $cart->tax_amount,
            'shipping_amount' => $cart->shipping_amount,
            'total_amount' => $cart->total_amount,
            'days_since_created' => $cart->created_at->diffInDays(now()),
            'days_since_last_activity' => $cart->last_activity_at ? $cart->last_activity_at->diffInDays(now()) : null,
        ];

        return view('dashboard.carts.show', compact('cart', 'cartStats'));
    }

    /**
     * Mark cart as abandoned.
     */
    public function markAsAbandoned(Cart $cart)
    {
        $cart->markAsAbandoned();

        $message = 'تم وضع علامة على السلة كمتروكة بنجاح';
        
        // إضافة رسالة إذا تم إرسال إيميل
        if ($cart->user && $cart->user->email) {
            $message .= ' وتم إرسال إيميل تذكيري للعميل';
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Restore abandoned cart.
     */
    public function restore(Cart $cart)
    {
        $cart->update([
            'is_abandoned' => false,
            'abandoned_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'تم استعادة السلة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return redirect()->route('dashboard.carts.index')
            ->with('success', 'تم حذف السلة بنجاح');
    }

    /**
     * Export carts to CSV.
     */
    public function export(Request $request)
    {
        $query = Cart::with(['user', 'items.product']);

        // تطبيق نفس الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('session_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'abandoned') {
                $query->abandoned();
            } elseif ($request->status === 'active') {
                $query->where('is_abandoned', false);
            }
        }

        $carts = $query->get();

        $filename = 'carts_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($carts) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'المستخدم',
                'معرف الجلسة',
                'كود الكوبون',
                'المبلغ الفرعي',
                'مبلغ الخصم',
                'مبلغ الضريبة',
                'مبلغ الشحن',
                'المبلغ الإجمالي',
                'العملة',
                'حالة السلة',
                'تاريخ الإنشاء',
                'آخر نشاط',
                'عدد المنتجات'
            ]);

            foreach ($carts as $cart) {
                fputcsv($file, [
                    $cart->user ? $cart->user->full_name : 'زائر',
                    $cart->session_id,
                    $cart->coupon_code,
                    $cart->subtotal,
                    $cart->discount_amount,
                    $cart->tax_amount,
                    $cart->shipping_amount,
                    $cart->total_amount,
                    $cart->currency,
                    $cart->is_abandoned ? 'متروكة' : 'نشطة',
                    $cart->created_at->format('Y-m-d H:i:s'),
                    $cart->last_activity_at?->format('Y-m-d H:i:s'),
                    $cart->items_count
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get abandoned carts statistics.
     */
    public function abandonedStats()
    {
        $stats = [
            'total_abandoned' => Cart::abandoned()->count(),
            'abandoned_value' => Cart::abandoned()->sum('total_amount'),
            'abandoned_last_24h' => Cart::abandoned()->where('abandoned_at', '>=', now()->subDay())->count(),
            'abandoned_last_week' => Cart::abandoned()->where('abandoned_at', '>=', now()->subWeek())->count(),
            'abandoned_last_month' => Cart::abandoned()->where('abandoned_at', '>=', now()->subMonth())->count(),
            'avg_abandoned_value' => Cart::abandoned()->avg('total_amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Clean up old abandoned carts.
     */
    public function cleanup()
    {
        $deletedCount = Cart::abandoned()
            ->where('abandoned_at', '<', now()->subDays(90))
            ->delete();

        return redirect()->back()
            ->with('success', "تم حذف {$deletedCount} سلة متروكة قديمة");
    }

    /**
     * Send notification to abandoned cart owner.
     */
    public function sendNotification(Request $request, Cart $cart)
    {
        Log::info("=== بدء إرسال إشعار للسلة المتروكة ===", [
            'cart_id' => $cart->id,
            'request_data' => $request->except(['_token']),
        ]);

        try {
            // التحقق من البيانات
            Log::info("التحقق من صحة البيانات المدخلة", [
                'cart_id' => $cart->id,
            ]);

            $request->validate([
                'channels' => 'required|array|min:1',
                'channels.*' => 'in:email',
                'template' => 'nullable|string|in:friendly_reminder,urgent_reminder,discount_offer,last_chance',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string',
                'priority' => 'nullable|in:low,normal,high,urgent',
            ], [
                'channels.required' => 'يجب اختيار قناة واحدة على الأقل',
                'channels.*.in' => 'القناة المحددة غير صحيحة - البريد الإلكتروني فقط',
                'template.in' => 'نموذج الإشعار غير صحيح',
                'message.required' => 'نص الإشعار مطلوب',
            ]);

            Log::info("تم التحقق من صحة البيانات بنجاح", [
                'cart_id' => $cart->id,
            ]);

            // تحميل العلاقات المطلوبة
            Log::info("تحميل العلاقات المطلوبة", [
                'cart_id' => $cart->id,
            ]);

            $cart->load(['user', 'items.product']);

            Log::info("تم تحميل العلاقات بنجاح", [
                'cart_id' => $cart->id,
                'has_user' => $cart->user_id !== null,
                'items_count' => $cart->items ? $cart->items->count() : 0,
            ]);

            if (!$cart->user_id) {
                Log::warning("السلة لا تحتوي على مستخدم", [
                    'cart_id' => $cart->id,
                ]);
                return response()->json(['error' => 'لا يمكن إرسال إشعار لسلة بدون مستخدم'], 400);
            }

            $user = $cart->user;
            
            Log::info("تم جلب بيانات المستخدم", [
                'cart_id' => $cart->id,
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null,
            ]);

            // التحقق من وجود إيميل للمستخدم
            if (!$user || !$user->email) {
                Log::warning("المستخدم لا يمتلك إيميل", [
                    'cart_id' => $cart->id,
                    'user_id' => $user->id ?? null,
                ]);
                return response()->json(['error' => 'لا يوجد إيميل للمستخدم'], 400);
            }
            
            $template = $request->template ?: 'friendly_reminder';
            $subject = $request->subject ?: 'تذكير: لديك منتجات في سلة التسوق';
            $message = $request->message;
            $priority = $request->priority ?: 'normal';

            Log::info("بيانات الإشعار جاهزة", [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
                'template' => $template,
                'subject' => $subject,
                'priority' => $priority,
                'message_length' => strlen($message),
            ]);

            $sentChannels = [];
            $failedChannels = [];

            // إرسال عبر البريد الإلكتروني فقط
            Log::info("بدء إرسال الإشعار عبر البريد الإلكتروني", [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            try {
                $this->sendNotificationToChannel($user, $cart, 'email', $template, $subject, $message, $priority);
                $sentChannels[] = 'email';
                
                Log::info("تم إرسال الإشعار بنجاح", [
                    'cart_id' => $cart->id,
                    'user_id' => $user->id,
                    'channel' => 'email',
                ]);
            } catch (\Exception $e) {
                $failedChannels[] = 'email';
                Log::error("فشل إرسال الإشعار عبر البريد الإلكتروني", [
                    'cart_id' => $cart->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            Log::info("=== انتهاء إرسال إشعار للسلة المتروكة ===", [
                'cart_id' => $cart->id,
                'sent_channels' => $sentChannels,
                'failed_channels' => $failedChannels,
            ]);

            return response()->json([
                'message' => 'تم إرسال الإشعار بنجاح',
                'sent_channels' => $sentChannels,
                'failed_channels' => $failedChannels,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("خطأ في التحقق من البيانات", [
                'cart_id' => $cart->id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("خطأ غير متوقع أثناء إرسال الإشعار", [
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Send bulk notifications to multiple abandoned carts.
     */
    public function sendBulkNotifications(Request $request)
    {
        Log::info("=== بدء إرسال إشعارات جماعية للسلات المتروكة ===", [
            'request_data' => $request->except(['_token']),
        ]);

        try {
            // التحقق من البيانات
            Log::info("التحقق من صحة البيانات المدخلة");

            $request->validate([
                'cart_ids' => 'required|array|min:1',
                'cart_ids.*' => 'exists:carts,id',
                'channels' => 'required|array|min:1',
                'channels.*' => 'in:email',
                'template' => 'nullable|string|in:friendly_reminder,urgent_reminder,discount_offer,last_chance',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string',
                'priority' => 'nullable|in:low,normal,high,urgent',
            ], [
                'cart_ids.required' => 'يجب اختيار سلة واحدة على الأقل',
                'cart_ids.*.exists' => 'السلة المحددة غير موجودة',
                'channels.required' => 'يجب اختيار قناة واحدة على الأقل',
                'channels.*.in' => 'القناة المحددة غير صحيحة - البريد الإلكتروني فقط',
                'template.in' => 'نموذج الإشعار غير صحيح',
                'message.required' => 'نص الإشعار مطلوب',
            ]);

            Log::info("تم التحقق من صحة البيانات بنجاح");

            $cartIds = $request->cart_ids;
            $template = $request->template ?: 'friendly_reminder';
            $subject = $request->subject ?: 'تذكير: لديك منتجات في سلة التسوق';
            $message = $request->message;
            $priority = $request->priority ?: 'normal';

            Log::info("بيانات الإشعار جاهزة", [
                'cart_ids_count' => count($cartIds),
                'template' => $template,
                'subject' => $subject,
                'priority' => $priority,
                'message_length' => strlen($message),
            ]);

            // جلب السلات
            Log::info("جلب بيانات السلات", [
                'cart_ids' => $cartIds,
            ]);

            $carts = Cart::with('user')->whereIn('id', $cartIds)->get();

            Log::info("تم جلب السلات بنجاح", [
                'total_carts' => $carts->count(),
            ]);

            $results = [
                'total_carts' => $carts->count(),
                'successful_sends' => 0,
                'failed_sends' => 0,
                'details' => []
            ];

            foreach ($carts as $index => $cart) {
                $cartNumber = $index + 1;
                Log::info("معالجة السلة رقم {$cartNumber} من {$results['total_carts']}", [
                    'cart_id' => $cart->id,
                    'cart_number' => $cartNumber,
                    'total_carts' => $results['total_carts'],
                ]);

                if (!$cart->user_id) {
                    Log::warning("السلة لا تحتوي على مستخدم", [
                        'cart_id' => $cart->id,
                        'cart_number' => $cartNumber,
                    ]);
                    $results['failed_sends']++;
                    $results['details'][] = [
                        'cart_id' => $cart->id,
                        'status' => 'failed',
                        'reason' => 'لا يوجد مستخدم مرتبط بالسلة'
                    ];
                    continue;
                }

                $user = $cart->user;
                
                Log::info("تم جلب بيانات المستخدم للسلة", [
                    'cart_id' => $cart->id,
                    'cart_number' => $cartNumber,
                    'user_id' => $user->id ?? null,
                    'user_email' => $user->email ?? null,
                ]);

                // التحقق من وجود إيميل للمستخدم - مطلوب للإرسال
                if (!$user || !$user->email) {
                    Log::warning("المستخدم لا يمتلك إيميل", [
                        'cart_id' => $cart->id,
                        'cart_number' => $cartNumber,
                        'user_id' => $user->id ?? null,
                    ]);
                    $results['failed_sends']++;
                    $results['details'][] = [
                        'cart_id' => $cart->id,
                        'user_name' => $user->full_name ?? 'غير محدد',
                        'status' => 'failed',
                        'reason' => 'لا يوجد إيميل للمستخدم'
                    ];
                    continue;
                }

                $sentChannels = [];
                $failedChannels = [];

                // إرسال عبر البريد الإلكتروني فقط
                Log::info("بدء إرسال الإشعار للسلة", [
                    'cart_id' => $cart->id,
                    'cart_number' => $cartNumber,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);

                try {
                    $this->sendNotificationToChannel($user, $cart, 'email', $template, $subject, $message, $priority);
                    $sentChannels[] = 'email';
                    
                    Log::info("تم إرسال الإشعار بنجاح للسلة", [
                        'cart_id' => $cart->id,
                        'cart_number' => $cartNumber,
                        'user_id' => $user->id,
                        'channel' => 'email',
                    ]);
                } catch (\Exception $e) {
                    $failedChannels[] = 'email';
                    Log::error("فشل إرسال الإشعار للسلة", [
                        'cart_id' => $cart->id,
                        'cart_number' => $cartNumber,
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                if (!empty($sentChannels)) {
                    $results['successful_sends']++;
                } else {
                    $results['failed_sends']++;
                }

                $results['details'][] = [
                    'cart_id' => $cart->id,
                    'user_name' => $user->full_name,
                    'user_email' => $user->email,
                    'sent_channels' => $sentChannels,
                    'failed_channels' => $failedChannels,
                ];
            }

            Log::info("=== انتهاء إرسال الإشعارات الجماعية ===", [
                'total_carts' => $results['total_carts'],
                'successful_sends' => $results['successful_sends'],
                'failed_sends' => $results['failed_sends'],
            ]);

            return response()->json([
                'message' => 'تم إرسال الإشعارات الجماعية',
                'results' => $results,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("خطأ في التحقق من البيانات", [
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("خطأ غير متوقع أثناء إرسال الإشعارات الجماعية", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Send notification to specific channel.
     */
    private function sendNotificationToChannel($user, $cart, $channel, $template, $subject, $message, $priority)
    {
        Log::info("--- بدء sendNotificationToChannel ---", [
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'channel' => $channel,
            'template' => $template,
        ]);

        // التأكد من تحميل العلاقات
        Log::info("التحقق من تحميل العلاقات", [
            'cart_id' => $cart->id,
            'items_loaded' => $cart->relationLoaded('items'),
        ]);

        if (!$cart->relationLoaded('items')) {
            Log::info("تحميل علاقة items", [
                'cart_id' => $cart->id,
            ]);
            $cart->load('items.product');
        }

        // التأكد من وجود items
        if (!$cart->items) {
            Log::warning("items غير موجودة، محاولة إعادة التحميل", [
                'cart_id' => $cart->id,
            ]);
            $cart->load('items.product');
        }

        Log::info("تم تحميل العلاقات بنجاح", [
            'cart_id' => $cart->id,
            'items_count' => $cart->items ? $cart->items->count() : 0,
        ]);

        // معالجة الرسالة
        Log::info("بدء معالجة قالب الرسالة", [
            'cart_id' => $cart->id,
            'template' => $template,
            'message_length' => strlen($message),
        ]);

        $processedMessage = $this->processMessageTemplate($message, $user, $cart, $template);

        Log::info("تم معالجة قالب الرسالة بنجاح", [
            'cart_id' => $cart->id,
            'processed_message_length' => strlen($processedMessage),
        ]);

        // معالجة cart_items
        Log::info("بدء معالجة عناصر السلة", [
            'cart_id' => $cart->id,
        ]);

        $cartItems = [];
        if ($cart->items && $cart->items->count() > 0) {
            $cartItems = $cart->items->map(function ($item) {
                return [
                    'product_name' => $item->product ? $item->product->name : 'منتج محذوف',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            })->toArray();
        }

        Log::info("تم معالجة عناصر السلة بنجاح", [
            'cart_id' => $cart->id,
            'items_count' => count($cartItems),
        ]);

        // Create notification record
        Log::info("بدء إنشاء سجل الإشعار في قاعدة البيانات", [
            'cart_id' => $cart->id,
            'user_id' => $user->id,
        ]);

        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'abandoned_cart',
                'title' => $subject,
                'message' => $processedMessage,
                'data' => [
                    'cart_id' => $cart->id,
                    'template' => $template,
                    'channel' => $channel,
                    'cart_items' => $cartItems,
                    'cart_total' => $cart->total_amount ?? 0,
                    'cart_currency' => $cart->currency ?? 'USD',
                ],
                'channel' => $channel,
                'priority' => $priority,
                'sent_at' => now(),
            ]);

            Log::info("تم إنشاء سجل الإشعار بنجاح", [
                'cart_id' => $cart->id,
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error("فشل إنشاء سجل الإشعار", [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        // Send via email only
        if ($channel === 'email') {
            Log::info("بدء إرسال الإيميل", [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            $this->sendEmailNotification($user, $cart, $subject, $processedMessage);

            Log::info("تم إرسال الإيميل بنجاح", [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
            ]);
        }

        Log::info("--- انتهاء sendNotificationToChannel ---", [
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'channel' => $channel,
        ]);
    }

    /**
     * Process message template with cart and user data.
     */
    private function processMessageTemplate($message, $user, $cart, $template)
    {
        Log::info("--- بدء processMessageTemplate ---", [
            'cart_id' => $cart->id,
            'template' => $template,
        ]);

        // التأكد من تحميل العلاقات
        if (!$cart->relationLoaded('items')) {
            Log::info("تحميل علاقة items في processMessageTemplate", [
                'cart_id' => $cart->id,
            ]);
            $cart->load('items.product');
        }

        // التأكد من وجود items
        if (!$cart->items) {
            Log::warning("items غير موجودة في processMessageTemplate", [
                'cart_id' => $cart->id,
            ]);
            $cart->load('items.product');
        }

        // حساب items_count
        $itemsCount = 0;
        if ($cart->items && $cart->items->count() > 0) {
            $itemsCount = $cart->items->sum('quantity');
        }

        Log::info("تم حساب عدد العناصر", [
            'cart_id' => $cart->id,
            'items_count' => $itemsCount,
        ]);

        // حساب total_amount
        $totalAmount = 0;
        if ($cart->items && $cart->items->count() > 0) {
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });
        }

        Log::info("تم حساب المبلغ الإجمالي", [
            'cart_id' => $cart->id,
            'total_amount' => $totalAmount,
        ]);

        $replacements = [
            '{{user_name}}' => $user->full_name ?? $user->email ?? 'عميلنا العزيز',
            '{{cart_total}}' => number_format($totalAmount, 2),
            '{{cart_currency}}' => $cart->currency ?? 'USD',
            '{{cart_items_count}}' => $itemsCount,
            '{{cart_url}}' => route('cart.index'),
            '{{checkout_url}}' => route('checkout.index'),
            '{{site_name}}' => config('app.name', 'متجر البطاقات الرقمية'),
        ];

        Log::info("تم إعداد replacements", [
            'cart_id' => $cart->id,
            'replacements_count' => count($replacements),
        ]);

        // Add cart items to replacements
        $itemsList = '';
        if ($cart->items && $cart->items->count() > 0) {
            Log::info("بدء بناء قائمة العناصر", [
                'cart_id' => $cart->id,
                'items_count' => $cart->items->count(),
            ]);

            foreach ($cart->items as $item) {
                $productName = $item->product ? $item->product->name : 'منتج محذوف';
                $currency = $cart->currency ?? 'USD';
                $itemsList .= "• {$productName} (الكمية: {$item->quantity}) - " . number_format($item->price, 2) . " {$currency}\n";
            }

            Log::info("تم بناء قائمة العناصر", [
                'cart_id' => $cart->id,
                'items_list_length' => strlen($itemsList),
            ]);
        }
        $replacements['{{cart_items_list}}'] = $itemsList;

        $processedMessage = str_replace(array_keys($replacements), array_values($replacements), $message);

        Log::info("--- انتهاء processMessageTemplate ---", [
            'cart_id' => $cart->id,
            'original_length' => strlen($message),
            'processed_length' => strlen($processedMessage),
        ]);

        return $processedMessage;
    }

    /**
     * Send email notification.
     */
    private function sendEmailNotification($user, $cart, $subject, $message)
    {
        Log::info("--- بدء sendEmailNotification ---", [
            'user_id' => $user->id ?? null,
            'cart_id' => $cart->id ?? null,
            'user_email' => $user->email ?? null,
        ]);

        // التحقق من وجود إيميل للمستخدم
        if (!$user || !$user->email) {
            Log::error("لا يوجد إيميل للمستخدم", [
                'user_id' => $user->id ?? null,
                'cart_id' => $cart->id ?? null,
            ]);
            throw new \Exception('لا يوجد إيميل للمستخدم');
        }

        Log::info("تم التحقق من وجود الإيميل", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'cart_id' => $cart->id,
        ]);

        // التأكد من تحميل العلاقات
        if (!$cart->relationLoaded('items')) {
            Log::info("تحميل علاقة items في sendEmailNotification", [
                'cart_id' => $cart->id,
            ]);
            $cart->load('items.product');
        }

        Log::info("بدء إرسال الإيميل عبر Laravel Mail", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'cart_id' => $cart->id,
            'subject' => $subject,
        ]);

        try {
            // إرسال الإيميل
            Mail::to($user->email)->send(new \App\Mail\AbandonedCartMail($user, $cart, $subject, $message));
            
            Log::info("تم إرسال الإيميل بنجاح عبر Laravel Mail", [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'cart_id' => $cart->id,
                'subject' => $subject,
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("فشل إرسال الإيميل عبر Laravel Mail", [
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null,
                'cart_id' => $cart->id ?? null,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        Log::info("--- انتهاء sendEmailNotification ---", [
            'user_id' => $user->id,
            'cart_id' => $cart->id,
        ]);
    }

    /**
     * Send SMS notification.
     */
    private function sendSmsNotification($user, $message)
    {
        if (!$user->phone) {
            throw new \Exception('لا يوجد رقم هاتف للمستخدم');
        }

        // Here you would implement actual SMS sending
        // For now, we'll just log it
        Log::info("SMS notification sent to {$user->phone}: {$message}");

        // In a real implementation, you would use an SMS service like:
        // SMS::send($user->phone, $message);
    }

    /**
     * Send WhatsApp notification.
     */
    private function sendWhatsAppNotification($user, $message)
    {
        if (!$user->phone) {
            throw new \Exception('لا يوجد رقم هاتف للمستخدم');
        }

        // Here you would implement actual WhatsApp sending
        // For now, we'll just log it
        Log::info("WhatsApp notification sent to {$user->phone}: {$message}");

        // In a real implementation, you would use a WhatsApp API service
    }

    /**
     * Get notification templates.
     */
    public function getNotificationTemplates()
    {
        $templates = [
            'friendly_reminder' => [
                'name' => 'تذكير ودود',
                'subject' => 'تذكير: لديك منتجات في سلة التسوق',
                'message' => "مرحباً {{user_name}}،\n\nنلاحظ أن لديك منتجات في سلة التسوق بقيمة {{cart_total}} {{cart_currency}}.\n\nالمنتجات:\n{{cart_items_list}}\n\nهل تريد إكمال عملية الشراء؟\n\nرابط السلة: {{cart_url}}\nرابط الدفع: {{checkout_url}}\n\nشكراً لك،\nفريق {{site_name}}"
            ],
            'urgent_reminder' => [
                'name' => 'تذكير عاجل',
                'subject' => 'عاجل: سلة التسوق الخاصة بك تنتظرك!',
                'message' => "{{user_name}}،\n\nلديك {{cart_items_count}} منتج في سلة التسوق بقيمة {{cart_total}} {{cart_currency}}!\n\n{{cart_items_list}}\n\nلا تفوت هذه الفرصة! أكمل عملية الشراء الآن:\n{{checkout_url}}\n\n{{site_name}}"
            ],
            'discount_offer' => [
                'name' => 'عرض خصم',
                'subject' => 'خصم خاص على سلة التسوق الخاصة بك!',
                'message' => "مرحباً {{user_name}}،\n\nلديك منتجات في سلة التسوق بقيمة {{cart_total}} {{cart_currency}}.\n\nنقدم لك خصم خاص 10% على طلبك!\n\nالمنتجات:\n{{cart_items_list}}\n\nاستخدم كود الخصم: CART10\n\nأكمل عملية الشراء الآن: {{checkout_url}}\n\nالعرض صالح لمدة 24 ساعة فقط!\n\n{{site_name}}"
            ],
            'last_chance' => [
                'name' => 'الفرصة الأخيرة',
                'subject' => 'الفرصة الأخيرة: سلة التسوق الخاصة بك!',
                'message' => "{{user_name}}،\n\nهذه فرصتك الأخيرة لإكمال عملية الشراء!\n\nلديك {{cart_items_count}} منتج في سلة التسوق بقيمة {{cart_total}} {{cart_currency}}.\n\n{{cart_items_list}}\n\nإذا لم تكمل عملية الشراء خلال 24 ساعة، سنقوم بإزالة المنتجات من سلة التسوق.\n\nأكمل عملية الشراء الآن: {{checkout_url}}\n\n{{site_name}}"
            ]
        ];

        return response()->json($templates);
    }
}
