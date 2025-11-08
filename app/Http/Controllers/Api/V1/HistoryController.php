<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Models\SearchHistory;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends BaseController
{
    /**
     * Get user activity history
     */
    public function index(Request $request)
    {
        if (!$request->user()) {
            return $this->unauthorizedResponse('يجب تسجيل الدخول');
        }

        $type = $request->input('type', 'all'); // all, searches, orders, views
        $limit = $request->input('limit', 50);

        $history = [];

        // Search History
        if ($type === 'all' || $type === 'searches') {
            $searchHistory = SearchHistory::where('user_id', $request->user()->id)
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'search',
                        'query' => $item->query,
                        'results_count' => $item->results_count,
                        'created_at' => $item->created_at->toIso8601String(),
                    ];
                });

            $history = array_merge($history, $searchHistory->toArray());
        }

        // Order History
        if ($type === 'all' || $type === 'orders') {
            $orders = Order::where('user_id', $request->user()->id)
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'order',
                        'order_number' => $item->order_number,
                        'status' => $item->status,
                        'total' => (float) $item->total,
                        'created_at' => $item->created_at->toIso8601String(),
                    ];
                });

            $history = array_merge($history, $orders->toArray());
        }

        // Sort by created_at descending
        usort($history, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        // Limit results
        $history = array_slice($history, 0, $limit);

        return $this->successResponse(['history' => $history]);
    }

    /**
     * Get search history only
     */
    public function searches(Request $request)
    {
        if (!$request->user()) {
            return $this->unauthorizedResponse('يجب تسجيل الدخول');
        }

        $history = SearchHistory::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        $formatted = $history->map(function($item) {
            return [
                'id' => $item->id,
                'query' => $item->query,
                'results_count' => $item->results_count,
                'created_at' => $item->created_at->toIso8601String(),
            ];
        });

        return $this->paginatedResponse($history, null, 'تم جلب سجل البحث بنجاح');
    }

    /**
     * Get order history only
     */
    public function orders(Request $request)
    {
        if (!$request->user()) {
            return $this->unauthorizedResponse('يجب تسجيل الدخول');
        }

        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($orders, null, 'تم جلب سجل الطلبات بنجاح');
    }

    /**
     * Clear all history
     */
    public function clear(Request $request)
    {
        if (!$request->user()) {
            return $this->unauthorizedResponse('يجب تسجيل الدخول');
        }

        $type = $request->input('type', 'all'); // all, searches, orders

        if ($type === 'all' || $type === 'searches') {
            SearchHistory::where('user_id', $request->user()->id)->delete();
        }

        // Note: Orders should not be deleted, only search history

        return $this->successResponse(null, 'تم حذف السجل بنجاح');
    }
}

