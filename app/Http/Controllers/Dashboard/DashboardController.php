<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        // يمكنك إضافة منطق لجلب البيانات من قاعدة البيانات هنا
        $stats = [
            'total_sales' => 1234,
            'new_orders' => 567,
            'products_count' => 89,
            'customers_count' => 2456,
        ];

        $recent_orders = [
            [
                'id' => 1234,
                'customer' => 'أحمد محمد',
                'amount' => 150.00,
                'status' => 'completed',
                'date' => '2024-01-15',
            ],
            [
                'id' => 1233,
                'customer' => 'فاطمة علي',
                'amount' => 75.50,
                'status' => 'pending',
                'date' => '2024-01-15',
            ],
            [
                'id' => 1232,
                'customer' => 'محمد السعد',
                'amount' => 200.00,
                'status' => 'cancelled',
                'date' => '2024-01-14',
            ],
            [
                'id' => 1231,
                'customer' => 'نورا أحمد',
                'amount' => 120.75,
                'status' => 'completed',
                'date' => '2024-01-14',
            ],
        ];

        $top_products = [
            [
                'name' => 'بطاقة هدايا أمازون',
                'price' => 50,
                'sales' => 45,
            ],
            [
                'name' => 'بطاقة شحن ستيم',
                'price' => 100,
                'sales' => 32,
            ],
            [
                'name' => 'بطاقة آيتونز',
                'price' => 75,
                'sales' => 28,
            ],
        ];

        return view('dashboard.index', compact('stats', 'recent_orders', 'top_products'));
    }
}
