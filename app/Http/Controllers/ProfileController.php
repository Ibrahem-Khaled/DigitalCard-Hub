<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use App\Services\ZohoBooksService;
use App\Models\Setting;
use App\Models\Order;
use Exception;

class ProfileController extends Controller
{
    /**
     * عرض الملف الشخصي
     */
    public function index()
    {
        $user = Auth::user();
        $user->load([
            'orders' => function($query) {
                $query->latest()->take(5);
            },
            'loyaltyPoints',
            'referrals'
        ]);

        // حساب نقاط الولاء
        $totalPoints = $user->loyaltyPoints()
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum('points');

        // إحصائيات الطلبات
        $ordersStats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'processing' => $user->orders()->where('status', 'processing')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
        ];

        return view('profile.index', compact('user', 'totalPoints', 'ordersStats'));
    }

    /**
     * عرض صفحة تعديل الملف الشخصي
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Prepare phone number with country code
        $phone = $request->input('phone');
        $countryCode = $request->input('country_code');
        $phoneNumber = $request->input('phone_number');
        
        // If phone is not directly provided, combine country code and phone number
        if (empty($phone) && !empty($phoneNumber)) {
            $phone = $countryCode . preg_replace('/^0+/', '', $phoneNumber);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'country_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Update phone in validated data
        $validated['phone'] = $phone;
        
        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض صفحة تغيير كلمة المرور
     */
    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * عرض الطلبات
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = $user->orders()->with(['orderItems.product']);

        // فلترة حسب حالة الطلب
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // فلترة حسب التاريخ (من)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // فلترة حسب التاريخ (إلى)
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (!in_array($sortBy, ['created_at', 'total_amount', 'order_number'], true)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(10)->withQueryString();

        return view('profile.orders', compact('orders'));
    }

    /**
     * عرض تفاصيل طلب معين
     */
    public function orderDetails($id)
    {
        $user = Auth::user();
        $order = $user->orders()
            ->with(['orderItems.product.category', 'payments'])
            ->findOrFail($id);

        return view('profile.order-details', compact('order'));
    }

    /**
     * عرض نقاط الولاء
     */
    public function loyaltyPoints()
    {
        $user = Auth::user();
        $loyaltyPoints = $user->loyaltyPoints()
            ->latest()
            ->paginate(10);

        $totalPoints = $user->loyaltyPoints()
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum('points');

        return view('profile.loyalty-points', compact('loyaltyPoints', 'totalPoints'));
    }

    /**
     * عرض الإحالات
     */
    public function referrals()
    {
        $user = Auth::user();
        $referrals = $user->referrals()
            ->with(['referred', 'referralReward'])
            ->latest()
            ->paginate(10);

        $referralCode = $user->getReferralCodeAttribute();

        return view('profile.referrals', compact('referrals', 'referralCode'));
    }

    /**
     * إنشاء فاتورة في Zoho Books وتحميلها
     * 
     * @param int $id Order ID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function createZohoInvoice($id)
    {
        try {
            $user = Auth::user();
            $order = $user->orders()
                ->with(['orderItems.product', 'user'])
                ->findOrFail($id);

            // التحقق من أن الطلب مدفوع
            if ($order->payment_status !== 'paid') {
                return back()->with('error', 'لا يمكن إنشاء فاتورة لطلب غير مدفوع');
            }

            // التحقق من وجود Zoho Invoice ID مسبقاً
            if ($order->zoho_invoice_id) {
                $zohoService = new ZohoBooksService();
                try {
                    $pdfContent = $zohoService->downloadInvoicePdf($order->zoho_invoice_id);
                    $filename = 'فاتورة_' . $order->order_number . '.pdf';
                    
                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                } catch (Exception $e) {
                    Log::warning('Failed to download existing Zoho invoice, creating new one', [
                        'order_id' => $order->id,
                        'zoho_invoice_id' => $order->zoho_invoice_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // إنشاء فاتورة جديدة في Zoho
            $zohoService = new ZohoBooksService();

            // إعداد بيانات العميل
            $billingAddress = is_array($order->billing_address) 
                ? $order->billing_address 
                : (is_string($order->billing_address) ? json_decode($order->billing_address, true) : []);

            $customerName = '';
            $customerEmail = '';
            $customerPhone = '';
            $customerAddress = '';
            $customerCity = '';
            $customerCountry = '';
            $customerPostalCode = '';

            if ($order->user) {
                $customerName = trim(($order->user->first_name ?? '') . ' ' . ($order->user->last_name ?? ''));
                $customerEmail = $order->user->email ?? '';
                $customerPhone = $order->user->phone ?? '';
                $customerAddress = $order->user->address ?? '';
                $customerCity = $order->user->city ?? '';
                $customerCountry = $order->user->country ?? '';
                $customerPostalCode = $order->user->postal_code ?? '';
            }

            if (empty($customerName) && !empty($billingAddress)) {
                $customerName = trim(($billingAddress['first_name'] ?? '') . ' ' . ($billingAddress['last_name'] ?? ''));
                $customerEmail = $billingAddress['email'] ?? $customerEmail;
                $customerPhone = $billingAddress['phone'] ?? $customerPhone;
                $customerAddress = $billingAddress['address'] ?? $customerAddress;
                $customerCity = $billingAddress['city'] ?? $customerCity;
                $customerCountry = $billingAddress['country'] ?? $customerCountry;
                $customerPostalCode = $billingAddress['postal_code'] ?? $customerPostalCode;
            }

            if (empty($customerName)) {
                $customerName = 'Customer - ' . $order->order_number;
            }

            // إنشاء أو الحصول على العميل في Zoho
            $customer = $zohoService->createOrGetCustomer([
                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
                'address' => $customerAddress,
                'city' => $customerCity,
                'country' => $customerCountry,
                'postal_code' => $customerPostalCode,
            ]);

            // إعداد عناصر الفاتورة
            $lineItems = [];
            foreach ($order->orderItems as $item) {
                $productName = $item->product ? $item->product->name : 'Product';
                // Use English description or product name
                $description = $item->product ? ($item->product->description ?? $productName) : 'Digital Card';
                $lineItems[] = [
                    'name' => $productName,
                    'description' => $description,
                    'rate' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                ];
            }

            // الحصول على معلومات المتجر (English for invoice)
            $storeName = Setting::get('store_name', 'Digital Cards Store');
            $storeNotes = Setting::get('invoice_notes', 'Thank you for your purchase!');

            // إنشاء الفاتورة في Zoho (All in English)
            $invoiceTemplateId = config('services.zoho.invoice_template_id');
            $currencyCode = $order->currency ?? 'USD'; // Use order currency or default to USD
            
            $invoice = $zohoService->createInvoice([
                'customer_id' => $customer['contact_id'],
                'invoice_number' => $order->order_number,
                'date' => $order->created_at->format('Y-m-d'),
                'due_date' => $order->created_at->copy()->addDays(30)->format('Y-m-d'),
                'currency_code' => $currencyCode,
                'line_items' => $lineItems,
                'notes' => $storeNotes,
                'terms' => 'Tax Invoice - ' . $storeName,
                'template_id' => $invoiceTemplateId,
            ]);

            // حفظ Zoho Invoice ID في الطلب
            $order->update([
                'zoho_invoice_id' => $invoice['invoice_id'],
                'zoho_invoice_number' => $invoice['invoice_number'] ?? null,
            ]);

            // تحميل PDF
            $pdfContent = $zohoService->downloadInvoicePdf($invoice['invoice_id']);
            $filename = 'فاتورة_' . $order->order_number . '.pdf';

            Log::info('Zoho invoice created and downloaded successfully', [
                'order_id' => $order->id,
                'zoho_invoice_id' => $invoice['invoice_id'],
                'zoho_invoice_number' => $invoice['invoice_number'] ?? null,
            ]);

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (Exception $e) {
            Log::error('Failed to create Zoho invoice', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'فشل إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * تحميل الفاتورة من الإيميل (Public - بدون auth)
     * 
     * @param Order $order
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoicePublic(Order $order, Request $request)
    {
        try {
            // التحقق من Token
            $token = $request->query('token');
            $expectedToken = md5($order->order_number . $order->created_at);
            
            if ($token !== $expectedToken) {
                abort(403, 'غير مصرح بالوصول إلى هذه الفاتورة');
            }

            // التحقق من أن الطلب مدفوع
            if ($order->payment_status !== 'paid' && $order->payment_status !== 'free') {
                abort(403, 'الفاتورة متاحة فقط للطلبات المدفوعة');
            }

            $order->load(['orderItems.product', 'user']);

            // التحقق من وجود Zoho Invoice ID مسبقاً
            if ($order->zoho_invoice_id) {
                $zohoService = new \App\Services\ZohoBooksService();
                try {
                    $pdfContent = $zohoService->downloadInvoicePdf($order->zoho_invoice_id);
                    $filename = 'فاتورة_' . $order->order_number . '.pdf';
                    
                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to download existing Zoho invoice, creating new one', [
                        'order_id' => $order->id,
                        'zoho_invoice_id' => $order->zoho_invoice_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // إنشاء فاتورة جديدة في Zoho (نفس الكود من createZohoInvoice)
            $zohoService = new \App\Services\ZohoBooksService();

            // إعداد بيانات العميل
            $billingAddress = is_array($order->billing_address) 
                ? $order->billing_address 
                : (is_string($order->billing_address) ? json_decode($order->billing_address, true) : []);

            $customerName = '';
            $customerEmail = '';
            $customerPhone = '';
            $customerAddress = '';
            $customerCity = '';
            $customerCountry = '';
            $customerPostalCode = '';

            if ($order->user) {
                $customerName = trim(($order->user->first_name ?? '') . ' ' . ($order->user->last_name ?? ''));
                $customerEmail = $order->user->email ?? '';
                $customerPhone = $order->user->phone ?? '';
                $customerAddress = $order->user->address ?? '';
                $customerCity = $order->user->city ?? '';
                $customerCountry = $order->user->country ?? '';
                $customerPostalCode = $order->user->postal_code ?? '';
            }

            if (empty($customerName) && !empty($billingAddress)) {
                $customerName = trim(($billingAddress['first_name'] ?? '') . ' ' . ($billingAddress['last_name'] ?? ''));
                $customerEmail = $billingAddress['email'] ?? $customerEmail;
                $customerPhone = $billingAddress['phone'] ?? $customerPhone;
                $customerAddress = $billingAddress['address'] ?? $customerAddress;
                $customerCity = $billingAddress['city'] ?? $customerCity;
                $customerCountry = $billingAddress['country'] ?? $customerCountry;
                $customerPostalCode = $billingAddress['postal_code'] ?? $customerPostalCode;
            }

            if (empty($customerName)) {
                $customerName = 'Customer - ' . $order->order_number;
            }

            // إنشاء أو الحصول على العميل في Zoho
            $customer = $zohoService->createOrGetCustomer([
                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
                'address' => $customerAddress,
                'city' => $customerCity,
                'country' => $customerCountry,
                'postal_code' => $customerPostalCode,
            ]);

            // إعداد عناصر الفاتورة
            $lineItems = [];
            foreach ($order->orderItems as $item) {
                $productName = $item->product ? $item->product->name : 'Product';
                $description = $item->product ? ($item->product->description ?? $productName) : 'Digital Card';
                $lineItems[] = [
                    'name' => $productName,
                    'description' => $description,
                    'rate' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                ];
            }

            // الحصول على معلومات المتجر
            $storeName = \App\Models\Setting::get('store_name', 'Digital Cards Store');
            $storeNotes = \App\Models\Setting::get('invoice_notes', 'Thank you for your purchase!');

            // إنشاء الفاتورة في Zoho
            $invoiceTemplateId = config('services.zoho.invoice_template_id');
            $currencyCode = $order->currency ?? 'USD';
            
            $invoice = $zohoService->createInvoice([
                'customer_id' => $customer['contact_id'],
                'invoice_number' => $order->order_number,
                'date' => $order->created_at->format('Y-m-d'),
                'due_date' => $order->created_at->copy()->addDays(30)->format('Y-m-d'),
                'currency_code' => $currencyCode,
                'line_items' => $lineItems,
                'notes' => $storeNotes,
                'terms' => 'Tax Invoice - ' . $storeName,
                'template_id' => $invoiceTemplateId,
            ]);

            // حفظ Zoho Invoice ID في الطلب
            $order->update([
                'zoho_invoice_id' => $invoice['invoice_id'],
                'zoho_invoice_number' => $invoice['invoice_number'] ?? null,
            ]);

            // تحميل PDF
            $pdfContent = $zohoService->downloadInvoicePdf($invoice['invoice_id']);
            $filename = 'فاتورة_' . $order->order_number . '.pdf';

            \Illuminate\Support\Facades\Log::info('Zoho invoice created and downloaded successfully (from email)', [
                'order_id' => $order->id,
                'zoho_invoice_id' => $invoice['invoice_id'],
                'zoho_invoice_number' => $invoice['invoice_number'] ?? null,
            ]);

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create Zoho invoice (from email)', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'فشل إنشاء الفاتورة: ' . $e->getMessage());
        }
    }
}

