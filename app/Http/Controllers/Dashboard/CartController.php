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
        $request->validate([
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:email,sms,whatsapp,database',
            'template' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ], [
            'channels.required' => 'يجب اختيار قناة واحدة على الأقل',
            'channels.*.in' => 'القناة المحددة غير صحيحة',
            'template.required' => 'نموذج الإشعار مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
        ]);

        // تحميل العلاقات المطلوبة
        $cart->load(['user', 'items.product']);

        if (!$cart->user_id) {
            return response()->json(['error' => 'لا يمكن إرسال إشعار لسلة بدون مستخدم'], 400);
        }

        $user = $cart->user;
        
        // التحقق من وجود إيميل للمستخدم
        if (!$user || !$user->email) {
            return response()->json(['error' => 'لا يوجد إيميل للمستخدم'], 400);
        }
        $channels = $request->channels;
        $template = $request->template;
        $subject = $request->subject ?: 'تذكير: لديك منتجات في سلة التسوق';
        $message = $request->message;
        $priority = $request->priority ?: 'normal';

        $sentChannels = [];
        $failedChannels = [];

        foreach ($channels as $channel) {
            try {
                $this->sendNotificationToChannel($user, $cart, $channel, $template, $subject, $message, $priority);
                $sentChannels[] = $channel;
            } catch (\Exception $e) {
                $failedChannels[] = $channel;
                Log::error("Failed to send notification via {$channel}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'تم إرسال الإشعار بنجاح',
            'sent_channels' => $sentChannels,
            'failed_channels' => $failedChannels,
        ]);
    }

    /**
     * Send bulk notifications to multiple abandoned carts.
     */
    public function sendBulkNotifications(Request $request)
    {
        $request->validate([
            'cart_ids' => 'required|array|min:1',
            'cart_ids.*' => 'exists:carts,id',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:email,sms,whatsapp,database',
            'template' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ], [
            'cart_ids.required' => 'يجب اختيار سلة واحدة على الأقل',
            'cart_ids.*.exists' => 'السلة المحددة غير موجودة',
            'channels.required' => 'يجب اختيار قناة واحدة على الأقل',
            'channels.*.in' => 'القناة المحددة غير صحيحة',
            'template.required' => 'نموذج الإشعار مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
        ]);

        $cartIds = $request->cart_ids;
        $channels = $request->channels;
        $template = $request->template;
        $subject = $request->subject ?: 'تذكير: لديك منتجات في سلة التسوق';
        $message = $request->message;
        $priority = $request->priority ?: 'normal';

        $carts = Cart::with('user')->whereIn('id', $cartIds)->get();
        $results = [
            'total_carts' => $carts->count(),
            'successful_sends' => 0,
            'failed_sends' => 0,
            'details' => []
        ];

        foreach ($carts as $cart) {
            if (!$cart->user_id) {
                $results['failed_sends']++;
                $results['details'][] = [
                    'cart_id' => $cart->id,
                    'status' => 'failed',
                    'reason' => 'لا يوجد مستخدم مرتبط بالسلة'
                ];
                continue;
            }

            $user = $cart->user;
            $sentChannels = [];
            $failedChannels = [];

            foreach ($channels as $channel) {
                try {
                    $this->sendNotificationToChannel($user, $cart, $channel, $template, $subject, $message, $priority);
                    $sentChannels[] = $channel;
                } catch (\Exception $e) {
                    $failedChannels[] = $channel;
                    Log::error("Failed to send notification via {$channel} to cart {$cart->id}: " . $e->getMessage());
                }
            }

            if (!empty($sentChannels)) {
                $results['successful_sends']++;
            } else {
                $results['failed_sends']++;
            }

            $results['details'][] = [
                'cart_id' => $cart->id,
                'user_name' => $user->full_name,
                'sent_channels' => $sentChannels,
                'failed_channels' => $failedChannels,
            ];
        }

        return response()->json([
            'message' => 'تم إرسال الإشعارات الجماعية',
            'results' => $results,
        ]);
    }

    /**
     * Send notification to specific channel.
     */
    private function sendNotificationToChannel($user, $cart, $channel, $template, $subject, $message, $priority)
    {
        // التأكد من تحميل العلاقات
        if (!$cart->relationLoaded('items')) {
            $cart->load('items.product');
        }

        // Create notification record
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'abandoned_cart',
            'title' => $subject,
            'message' => $this->processMessageTemplate($message, $user, $cart, $template),
            'data' => [
                'cart_id' => $cart->id,
                'template' => $template,
                'channel' => $channel,
                'cart_items' => $cart->items->map(function ($item) {
                    return [
                        'product_name' => $item->product ? $item->product->name : 'منتج محذوف',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
                'cart_total' => $cart->total_amount,
                'cart_currency' => $cart->currency ?? 'USD',
            ],
            'channel' => $channel,
            'priority' => $priority,
            'sent_at' => now(),
        ]);

        // Send via specific channel
        switch ($channel) {
            case 'email':
                $this->sendEmailNotification($user, $cart, $subject, $notification->message);
                break;
            case 'sms':
                $this->sendSmsNotification($user, $notification->message);
                break;
            case 'whatsapp':
                $this->sendWhatsAppNotification($user, $notification->message);
                break;
            case 'database':
                // Already saved to database
                break;
        }
    }

    /**
     * Process message template with cart and user data.
     */
    private function processMessageTemplate($message, $user, $cart, $template)
    {
        // التأكد من تحميل العلاقات
        if (!$cart->relationLoaded('items')) {
            $cart->load('items.product');
        }

        $replacements = [
            '{{user_name}}' => $user->full_name ?? $user->email,
            '{{cart_total}}' => number_format($cart->total_amount ?? 0, 2),
            '{{cart_currency}}' => $cart->currency ?? 'USD',
            '{{cart_items_count}}' => $cart->items_count ?? 0,
            '{{cart_url}}' => route('cart.index'),
            '{{checkout_url}}' => route('checkout.index'),
            '{{site_name}}' => config('app.name', 'متجر البطاقات الرقمية'),
        ];

        // Add cart items to replacements
        $itemsList = '';
        if ($cart->items && $cart->items->count() > 0) {
            foreach ($cart->items as $item) {
                $productName = $item->product ? $item->product->name : 'منتج محذوف';
                $currency = $cart->currency ?? 'USD';
                $itemsList .= "• {$productName} (الكمية: {$item->quantity}) - " . number_format($item->price, 2) . " {$currency}\n";
            }
        }
        $replacements['{{cart_items_list}}'] = $itemsList;

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Send email notification.
     */
    private function sendEmailNotification($user, $cart, $subject, $message)
    {
        // التحقق من وجود إيميل للمستخدم
        if (!$user->email) {
            throw new \Exception('لا يوجد إيميل للمستخدم');
        }

        try {
            // إرسال الإيميل
            Mail::to($user->email)->send(new \App\Mail\AbandonedCartMail($user, $cart, $subject, $message));
            
            Log::info("Abandoned cart email sent successfully", [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'cart_id' => $cart->id,
                'subject' => $subject,
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send abandoned cart email", [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
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
