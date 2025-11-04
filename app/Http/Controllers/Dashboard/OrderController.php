<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'orderItems.product', 'payments']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('coupon_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب طريقة الدفع
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // فلترة حسب المبلغ
        if ($request->filled('amount_from')) {
            $query->where('total_amount', '>=', $request->amount_from);
        }
        if ($request->filled('amount_to')) {
            $query->where('total_amount', '<=', $request->amount_to);
        }

        // ترتيب
        $allowedSortColumns = ['created_at', 'total_amount', 'order_number'];
        $sortBy = $request->get('sort_by');
        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        $sortOrder = $request->get('sort_order');
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20)->withQueryString();

        // الإحصائيات
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'paid_orders' => Order::where('payment_status', 'paid')->count(),
            'pending_payments' => Order::where('payment_status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => Order::where('payment_status', 'paid')->avg('total_amount'),
        ];

        // البيانات للفلترة
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        $statuses = [
            'pending' => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد'
        ];
        $paymentStatuses = [
            'pending' => 'في الانتظار',
            'paid' => 'مدفوع',
            'failed' => 'فشل',
            'refunded' => 'مسترد'
        ];
        $paymentMethods = [
            'credit_card' => 'بطاقة ائتمان',
            'debit_card' => 'بطاقة خصم',
            'bank_transfer' => 'تحويل بنكي',
            'paypal' => 'باي بال',
            'stripe' => 'سترايب',
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'wallet' => 'محفظة رقمية',
            'loyalty_points' => 'نقاط الولاء'
        ];

        return view('dashboard.orders.index', compact(
            'orders', 'stats', 'users', 'statuses', 'paymentStatuses', 'paymentMethods'
        ));
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'orderItems.product',
            'orderItems.digitalCards',
            'payments',
            'couponUsage.coupon'
        ]);

        return view('dashboard.orders.show', compact('order'));
    }

    /**
     * عرض نموذج إنشاء طلب جديد
     */
    public function create(): View
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        $products = Product::where('is_active', true)->select('id', 'name', 'price', 'sku')->get();

        return view('dashboard.orders.create', compact('users', 'products'));
    }

    /**
     * حفظ الطلب الجديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // إنشاء الطلب
            $order = Order::create([
                'user_id' => $request->user_id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'subtotal' => 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'shipping_amount' => $request->shipping_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => 0,
                'currency' => $request->currency ?? 'USD',
                'coupon_code' => $request->coupon_code,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'notes' => $request->notes,
            ]);

            $subtotal = 0;

            // إضافة عناصر الطلب
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $itemPrice = $product->sale_price ?? $product->price;
                $itemTotal = $itemPrice * $item['quantity'];
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice,
                    'total_price' => $itemTotal,
                    'status' => 'pending',
                ]);
            }

            // تحديث إجماليات الطلب
            $totalAmount = $subtotal + ($order->tax_amount) + ($order->shipping_amount) - ($order->discount_amount);

            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
            ]);

            // إنشاء سجل الدفع إذا كان مدفوع
            if ($request->payment_status === 'paid') {
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'payment_method' => $order->payment_method,
                    'payment_gateway' => $request->payment_gateway ?? 'manual',
                    'amount' => $totalAmount,
                    'currency' => $order->currency,
                    'status' => 'successful',
                    'processed_at' => now(),
                ]);

                // إضافة نقاط الولاء للمستخدم
                if ($order->user_id) {
                    try {
                        LoyaltyPoint::addPointsForPurchase($order->user_id, $totalAmount, $order->id);
                        Log::info("Loyalty points added for order {$order->order_number} via admin create");
                    } catch (\Exception $e) {
                        Log::error('Error adding loyalty points: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();

            return redirect()->route('dashboard.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض نموذج تعديل الطلب
     */
    public function edit(Order $order): View
    {
        $order->load(['user', 'orderItems.product', 'payments']);
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        $products = Product::where('is_active', true)->select('id', 'name', 'price', 'sku')->get();

        return view('dashboard.orders.edit', compact('order', 'users', 'products'));
    }

    /**
     * تحديث الطلب
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $order->update([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            return redirect()->route('dashboard.orders.show', $order)
                ->with('success', 'تم تحديث الطلب بنجاح.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string'
        ]);

        $order->updateStatus($request->status);

        if ($request->notes) {
            $order->update(['notes' => $request->notes]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    /**
     * تحديث حالة الدفع
     */
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string'
        ]);

        $previousPaymentStatus = $order->payment_status;
        $order->updatePaymentStatus($request->payment_status);

        if ($request->notes) {
            $order->update(['notes' => $request->notes]);
        }

        // إضافة نقاط الولاء عند تغيير حالة الدفع إلى "مدفوع"
        if ($request->payment_status === 'paid' && $previousPaymentStatus !== 'paid' && $order->user_id) {
            try {
                // التحقق من عدم إضافة نقاط مسبقاً لهذا الطلب
                $existingPoints = LoyaltyPoint::where('user_id', $order->user_id)
                    ->where('source', 'purchase')
                    ->where('source_id', $order->id)
                    ->first();

                if (!$existingPoints) {
                    LoyaltyPoint::addPointsForPurchase($order->user_id, $order->total_amount, $order->id);
                    Log::info("Loyalty points added for order {$order->order_number} via admin update");
                }
            } catch (\Exception $e) {
                Log::error('Error adding loyalty points: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الدفع بنجاح.');
    }

    /**
     * تحديث حالة عنصر الطلب
     */
    public function updateItemStatus(Request $request, OrderItem $orderItem): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);

        $orderItem->updateStatus($request->status);

        return redirect()->back()->with('success', 'تم تحديث حالة العنصر بنجاح.');
    }

    /**
     * حذف الطلب
     */
    public function destroy(Order $order): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // حذف المدفوعات
            $order->payments()->delete();

            // حذف عناصر الطلب
            $order->orderItems()->delete();

            // حذف الطلب
            $order->delete();

            DB::commit();

            return redirect()->route('dashboard.orders.index')
                ->with('success', 'تم حذف الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الطلب: ' . $e->getMessage());
        }
    }

    /**
     * تصدير الطلبات
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product']);

        // تطبيق نفس الفلاتر من index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->get();

        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // رؤوس الأعمدة
            fputcsv($file, [
                'رقم الطلب', 'المستخدم', 'الحالة', 'حالة الدفع', 'المبلغ الإجمالي',
                'العملة', 'طريقة الدفع', 'تاريخ الإنشاء', 'تاريخ التحديث'
            ]);

            // البيانات
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->first_name . ' ' . $order->user->last_name,
                    $order->getStatusInArabic(),
                    $order->getPaymentStatusInArabic(),
                    $order->total_amount,
                    $order->currency,
                    $order->getPaymentMethodInArabic(),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * إحصائيات الطلبات
     */
    public function statistics(): View
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => Order::where('payment_status', 'paid')->avg('total_amount'),
            'orders_by_status' => Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'orders_by_payment_status' => Order::select('payment_status', DB::raw('count(*) as count'))
                ->groupBy('payment_status')
                ->pluck('count', 'payment_status'),
            'monthly_revenue' => Order::where('payment_status', 'paid')
                ->whereYear('created_at', date('Y'))
                ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                ->groupBy('month')
                ->pluck('revenue', 'month'),
        ];

        return view('dashboard.orders.statistics', compact('stats'));
    }
}
