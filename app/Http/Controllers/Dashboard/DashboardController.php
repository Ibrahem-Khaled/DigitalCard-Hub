<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\DigitalCard;
use App\Models\Payment;
use App\Models\Coupon;
use App\Models\LoyaltyPoint;
use App\Models\Notification;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // إحصائيات المبيعات
        $stats = [
            'total_sales' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'new_orders' => Order::whereDate('created_at', today())->count(),
            'products_count' => Product::where('is_active', true)->count(),
            'customers_count' => User::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'paid_orders' => Order::where('payment_status', 'paid')->count(),
            'pending_payments' => Order::where('payment_status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => Order::where('payment_status', 'paid')->avg('total_amount'),
            'digital_cards_count' => DigitalCard::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'loyalty_points_total' => LoyaltyPoint::where('is_active', true)->sum('points'),
            'unread_notifications' => Notification::where('read_at', null)->count(),
            'pending_contacts' => Contact::where('status', 'new')->count(),
        ];

        // الطلبات الأخيرة
        $recent_orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // تحويل البيانات لتكون متوافقة مع مكون الجدول
        $recentOrdersData = $recent_orders->map(function ($order) {
            $statusBadge = match($order->status) {
                'delivered' => '<span class="badge badge-success">تم التسليم</span>',
                'processing' => '<span class="badge badge-info">قيد المعالجة</span>',
                'shipped' => '<span class="badge badge-primary">تم الشحن</span>',
                'pending' => '<span class="badge badge-warning">في الانتظار</span>',
                'cancelled' => '<span class="badge badge-danger">ملغي</span>',
                'refunded' => '<span class="badge badge-secondary">مسترد</span>',
                default => '<span class="badge badge-info">' . $order->status . '</span>'
            };

            return [
                '<strong>#' . $order->order_number . '</strong>',
                $order->user->first_name . ' ' . $order->user->last_name,
                '<strong>' . number_format($order->total_amount, 2) . ' ' . $order->currency . '</strong>',
                $statusBadge,
                $order->created_at->format('Y-m-d'),
                '<a href="' . route('dashboard.orders.show', $order) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>'
            ];
        })->toArray();

        // المنتجات الأكثر مبيعاً
        $top_products = Product::withCount(['orderItems as sales_count' => function ($query) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('payment_status', 'paid');
                });
            }])
            ->where('is_active', true)
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'price' => $product->price,
                    'sales' => $product->sales_count,
                ];
            });

        // إحصائيات المبيعات الشهرية
        $monthly_sales = Order::where('payment_status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders_count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // إحصائيات المبيعات اليومية (آخر 30 يوم)
        $daily_sales = Order::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // إحصائيات العملاء
        $customer_stats = [
            'new_customers_today' => User::whereDate('created_at', today())->count(),
            'new_customers_this_month' => User::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'total_customers' => User::count(),
            'active_customers' => User::where('is_active', true)->count(),
        ];

        // إحصائيات المنتجات
        $product_stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'digital_products' => Product::where('is_digital', true)->count(),
            'products_with_loyalty_points' => Product::where('loyalty_points_earn', '>', 0)->count(),
        ];

        // النشاط الأخير
        $recent_activity = collect()
            ->merge(
                Order::with('user')->orderBy('created_at', 'desc')->limit(3)->get()->map(function ($order) {
                    return [
                        'type' => 'order',
                        'icon' => 'bi-cart-plus',
                        'color' => 'primary',
                        'title' => 'طلب جديد #' . $order->order_number,
                        'description' => 'تم إنشاء طلب جديد من العميل ' . $order->user->first_name . ' ' . $order->user->last_name,
                        'time' => $order->created_at->diffForHumans(),
                        'amount' => $order->total_amount,
                        'currency' => $order->currency,
                    ];
                })
            )
            ->merge(
                Payment::with(['order', 'user'])->where('status', 'successful')->orderBy('created_at', 'desc')->limit(2)->get()->map(function ($payment) {
                    return [
                        'type' => 'payment',
                        'icon' => 'bi-credit-card',
                        'color' => 'success',
                        'title' => 'دفعة جديدة',
                        'description' => 'تم استلام دفعة بقيمة ' . number_format($payment->amount, 2) . ' ' . $payment->currency,
                        'time' => $payment->created_at->diffForHumans(),
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                    ];
                })
            )
            ->merge(
                Product::orderBy('created_at', 'desc')->limit(2)->get()->map(function ($product) {
                    return [
                        'type' => 'product',
                        'icon' => 'bi-box-seam',
                        'color' => 'info',
                        'title' => 'منتج جديد',
                        'description' => 'تم إضافة منتج جديد: ' . $product->name,
                        'time' => $product->created_at->diffForHumans(),
                        'price' => $product->price,
                    ];
                })
            )
            ->sortByDesc(function ($item) {
                return $item['time'];
            })
            ->take(5);

        return view('dashboard.index', compact(
            'stats',
            'recent_orders',
            'recentOrdersData',
            'top_products',
            'monthly_sales',
            'daily_sales',
            'customer_stats',
            'product_stats',
            'recent_activity'
        ));
    }

    /**
     * عرض صفحة التقارير
     */
    public function reports(Request $request)
    {
        $dateFromInput = $request->get('date_from');
        $dateToInput = $request->get('date_to');

        $dateFrom = $dateFromInput
            ? Carbon::parse($dateFromInput)->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $dateTo = $dateToInput
            ? Carbon::parse($dateToInput)->endOfDay()
            : Carbon::now()->endOfMonth()->endOfDay();

        if ($dateFrom->gt($dateTo)) {
            $temp = $dateFrom->copy();
            $dateFrom = $dateTo->copy()->startOfDay();
            $dateTo = $temp->endOfDay();
        }

        $filters = [
            'category_id' => $request->get('category_id'),
            'card_provider' => $request->get('card_provider'),
            'card_type' => $request->get('card_type'),
            'card_region' => $request->get('card_region'),
            'payment_method' => $request->get('payment_method'),
        ];

        $orderItemsQuery = OrderItem::query()
            ->whereHas('order', function ($query) use ($dateFrom, $dateTo, $filters) {
                $query->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$dateFrom, $dateTo]);

                if (!empty($filters['payment_method'])) {
                    $query->where('payment_method', $filters['payment_method']);
                }
            });

        $productFilters = array_filter([
            'category_id' => $filters['category_id'],
            'card_provider' => $filters['card_provider'],
            'card_type' => $filters['card_type'],
            'card_region' => $filters['card_region'],
        ]);

        if (!empty($productFilters)) {
            $orderItemsQuery->whereHas('product', function ($query) use ($productFilters) {
                if (!empty($productFilters['category_id'])) {
                    $query->where('category_id', $productFilters['category_id']);
                }
                if (!empty($productFilters['card_provider'])) {
                    $query->where('card_provider', $productFilters['card_provider']);
                }
                if (!empty($productFilters['card_type'])) {
                    $query->where('card_type', $productFilters['card_type']);
                }
                if (!empty($productFilters['card_region'])) {
                    $query->where('card_region', $productFilters['card_region']);
                }
            });
        }

        $filteredOrderIds = (clone $orderItemsQuery)->distinct()->pluck('order_id');
        $filteredOrderIdsArray = $filteredOrderIds->all();
        $totalOrders = count($filteredOrderIdsArray);

        $totalRevenue = (clone $orderItemsQuery)->sum('total_price');
        $totalProductsSold = (clone $orderItemsQuery)->sum('quantity');

        $uniqueCustomers = !empty($filteredOrderIdsArray)
            ? Order::whereIn('id', $filteredOrderIdsArray)->distinct()->count('user_id')
            : 0;

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $averageItemsPerOrder = $totalOrders > 0 ? $totalProductsSold / $totalOrders : 0;

        $sales_report = (clone $orderItemsQuery)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('DATE(orders.created_at) as date')
            ->selectRaw('COUNT(DISTINCT order_items.order_id) as orders_count')
            ->selectRaw('SUM(order_items.quantity) as items_count')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->groupByRaw('DATE(orders.created_at)')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                $row->average_order_value = $row->orders_count > 0 ? (float) $row->total_revenue / $row->orders_count : 0;
                $row->date_label = Carbon::parse($row->date)->format('Y-m-d');
                return $row;
            });

        $products_report = (clone $orderItemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.product_id', 'products.name', 'products.price', 'products.sku', 'products.image', 'products.card_provider', 'products.card_type')
            ->selectRaw('SUM(order_items.quantity) as total_quantity')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->groupBy('order_items.product_id', 'products.name', 'products.price', 'products.sku', 'products.image', 'products.card_provider', 'products.card_type')
            ->orderByDesc('total_revenue')
            ->get();

        $customers_report = (clone $orderItemsQuery)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.user_id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.avatar')
            ->selectRaw('COUNT(DISTINCT order_items.order_id) as orders_count')
            ->selectRaw('SUM(order_items.total_price) as total_spent')
            ->selectRaw('SUM(order_items.quantity) as total_items')
            ->selectRaw('MAX(orders.created_at) as last_order_at')
            ->groupBy('orders.user_id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.avatar')
            ->orderByDesc('total_spent')
            ->get();

        $category_breakdown = (clone $orderItemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->selectRaw('SUM(order_items.quantity) as total_items')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $provider_breakdown = (clone $orderItemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.card_provider')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->selectRaw('SUM(order_items.quantity) as total_items')
            ->whereNotNull('products.card_provider')
            ->groupBy('products.card_provider')
            ->orderByDesc('total_revenue')
            ->get();

        $region_breakdown = (clone $orderItemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.card_region')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->selectRaw('SUM(order_items.quantity) as total_items')
            ->whereNotNull('products.card_region')
            ->groupBy('products.card_region')
            ->orderByDesc('total_revenue')
            ->get();

        $type_breakdown = (clone $orderItemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.card_type')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->selectRaw('SUM(order_items.quantity) as total_items')
            ->whereNotNull('products.card_type')
            ->groupBy('products.card_type')
            ->orderByDesc('total_revenue')
            ->get();

        $order_status_distribution = collect();

        if (!empty($filteredOrderIdsArray)) {
            $statusLabels = [
                'pending' => 'في الانتظار',
                'processing' => 'قيد المعالجة',
                'shipped' => 'تم الشحن',
                'delivered' => 'تم التسليم',
                'cancelled' => 'ملغي',
                'refunded' => 'مسترد',
            ];

            $statusColors = [
                'pending' => '#ffc107',
                'processing' => '#17a2b8',
                'shipped' => '#0d6efd',
                'delivered' => '#28a745',
                'cancelled' => '#dc3545',
                'refunded' => '#6c757d',
            ];

            $order_status_distribution = Order::whereIn('id', $filteredOrderIdsArray)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->map(function ($row) use ($statusLabels, $statusColors) {
                    $status = $row->status;
                    return [
                        'status' => $status,
                        'label' => $statusLabels[$status] ?? $status,
                        'total' => (int) $row->total,
                        'color' => $statusColors[$status] ?? '#0d6efd',
                    ];
                });
        }

        $sales_chart_data = [
            'labels' => $sales_report->map(fn ($row) => Carbon::parse($row->date)->format('m/d'))->toArray(),
            'revenue' => $sales_report->map(fn ($row) => round((float) $row->total_revenue, 2))->toArray(),
            'orders' => $sales_report->map(fn ($row) => (int) $row->orders_count)->toArray(),
        ];

        $order_status_chart = [
            'labels' => $order_status_distribution->pluck('label')->toArray(),
            'data' => $order_status_distribution->pluck('total')->toArray(),
            'colors' => $order_status_distribution->pluck('color')->toArray(),
        ];

        $filters_options = [
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
            'providers' => Product::whereNotNull('card_provider')->distinct()->orderBy('card_provider')->pluck('card_provider'),
            'types' => Product::whereNotNull('card_type')->distinct()->orderBy('card_type')->pluck('card_type'),
            'regions' => Product::whereNotNull('card_region')->distinct()->orderBy('card_region')->pluck('card_region'),
            'payment_methods' => [
                'credit_card' => 'بطاقة ائتمان',
                'debit_card' => 'بطاقة خصم',
                'bank_transfer' => 'تحويل بنكي',
                'paypal' => 'باي بال',
                'stripe' => 'سترايب',
                'cash_on_delivery' => 'الدفع عند الاستلام',
                'wallet' => 'محفظة رقمية',
                'loyalty_points' => 'نقاط الولاء',
            ],
        ];

        $active_filters = collect();

        if (!empty($filters['category_id'])) {
            $active_filters->push('الفئة: ' . optional(Category::find($filters['category_id']))->name);
        }
        if (!empty($filters['card_provider'])) {
            $active_filters->push('المزود: ' . $filters['card_provider']);
        }
        if (!empty($filters['card_type'])) {
            $active_filters->push('نوع البطاقة: ' . $filters['card_type']);
        }
        if (!empty($filters['card_region'])) {
            $active_filters->push('المنطقة: ' . $filters['card_region']);
        }
        if (!empty($filters['payment_method'])) {
            $active_filters->push('طريقة الدفع: ' . ($filters_options['payment_methods'][$filters['payment_method']] ?? $filters['payment_method']));
        }

        $period_stats = [
            'total_orders' => $totalOrders,
            'paid_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'total_products_sold' => $totalProductsSold,
            'unique_customers' => $uniqueCustomers,
            'average_items_per_order' => $averageItemsPerOrder,
        ];

        $top_insights = [
            'best_day' => $sales_report->sortByDesc('total_revenue')->first(),
            'top_category' => $category_breakdown->first(),
            'top_provider' => $provider_breakdown->first(),
            'top_region' => $region_breakdown->first(),
        ];

        $export_params = array_filter([
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'category_id' => $filters['category_id'],
            'card_provider' => $filters['card_provider'],
            'card_type' => $filters['card_type'],
            'card_region' => $filters['card_region'],
            'payment_method' => $filters['payment_method'],
        ]);

        return view('dashboard.reports', [
            'sales_report' => $sales_report,
            'products_report' => $products_report,
            'customers_report' => $customers_report,
            'category_breakdown' => $category_breakdown,
            'provider_breakdown' => $provider_breakdown,
            'region_breakdown' => $region_breakdown,
            'type_breakdown' => $type_breakdown,
            'order_status_chart' => $order_status_chart,
            'sales_chart_data' => $sales_chart_data,
            'period_stats' => $period_stats,
            'dateFrom' => $dateFrom->toDateString(),
            'dateTo' => $dateTo->toDateString(),
            'filters_options' => $filters_options,
            'active_filters' => $active_filters,
            'top_insights' => $top_insights,
            'export_params' => $export_params,
            'filters' => $filters,
        ]);
    }

    /**
     * تصدير تقرير المبيعات
     */
    public function exportSalesReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $orders = Order::with(['user', 'orderItems.product'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'sales_report_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // رؤوس الأعمدة
            fputcsv($file, [
                'رقم الطلب', 'العميل', 'البريد الإلكتروني', 'المبلغ الإجمالي', 'العملة',
                'حالة الطلب', 'حالة الدفع', 'طريقة الدفع', 'تاريخ الإنشاء', 'المنتجات'
            ]);

            // البيانات
            foreach ($orders as $order) {
                $products = $order->orderItems->map(function ($item) {
                    return $item->product->name . ' (x' . $item->quantity . ')';
                })->implode(', ');

                fputcsv($file, [
                    $order->order_number,
                    $order->user->first_name . ' ' . $order->user->last_name,
                    $order->user->email,
                    $order->total_amount,
                    $order->currency,
                    $order->getStatusInArabic(),
                    $order->getPaymentStatusInArabic(),
                    $order->getPaymentMethodInArabic(),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $products,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
