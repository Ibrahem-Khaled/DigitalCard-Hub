<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DigitalCard;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DigitalCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DigitalCard::with(['product', 'usedBy', 'orderItem']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('card_code', 'like', "%{$search}%")
                  ->orWhere('card_pin', 'like', "%{$search}%")
                  ->orWhere('card_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب المنتج
        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الاستخدام
        if ($request->filled('usage')) {
            if ($request->usage === 'used') {
                $query->where('is_used', true);
            } elseif ($request->usage === 'available') {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            } elseif ($request->usage === 'expired') {
                $query->where('expiry_date', '<', now());
            }
        }

        // فلترة حسب العملة
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'card_code', 'value', 'expiry_date', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $digitalCards = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_cards' => DigitalCard::count(),
            'available_cards' => DigitalCard::available()->count(),
            'used_cards' => DigitalCard::used()->count(),
            'expired_cards' => DigitalCard::expired()->count(),
            'active_cards' => DigitalCard::where('status', 'active')->count(),
            'inactive_cards' => DigitalCard::where('status', 'inactive')->count(),
        ];

        $products = Product::where('is_digital', true)->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.digital-cards.index', compact('digitalCards', 'stats', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('is_digital', true)->where('is_active', true)->orderBy('name')->get();
        return view('dashboard.digital-cards.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'card_code' => 'nullable|string|max:255',
            'card_pin' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'expiry_date' => 'nullable|date|after:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        $cardData = $request->except(['quantity']);
        $quantity = $request->quantity;

        // إنشاء البطاقات
        $createdCards = [];
        for ($i = 0; $i < $quantity; $i++) {
            $card = DigitalCard::create([
                'product_id' => $cardData['product_id'],
                'card_code' => $cardData['card_code'] ?: $this->generateCardCode(),
                'card_pin' => $cardData['card_pin'] ?: $this->generateCardPin(),
                'card_number' => $cardData['card_number'] ?: $this->generateCardNumber(),
                'serial_number' => $cardData['serial_number'] ?: $this->generateSerialNumber(),
                'value' => $cardData['value'],
                'currency' => $cardData['currency'],
                'expiry_date' => $cardData['expiry_date'],
                'status' => $cardData['status'],
                'notes' => $cardData['notes'],
            ]);
            $createdCards[] = $card;
        }

        return redirect()->route('dashboard.digital-cards.index')
            ->with('success', "تم إنشاء {$quantity} بطاقة رقمية بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalCard $digitalCard)
    {
        $digitalCard->load(['product', 'usedBy', 'orderItem', 'order']);

        return view('dashboard.digital-cards.show', compact('digitalCard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalCard $digitalCard)
    {
        $products = Product::where('is_digital', true)->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.digital-cards.edit', compact('digitalCard', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalCard $digitalCard)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'card_code' => 'nullable|string|max:255',
            'card_pin' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,used,expired',
            'notes' => 'nullable|string',
        ]);

        // إخفاء الأكواد من التحديث إذا كانت فارغة (لأسباب أمنية)
        $data = $request->all();
        
        // إذا كانت الحقول فارغة، لا يتم تحديثها (تبقى القيمة الحالية)
        if (empty($data['card_code'])) {
            unset($data['card_code']);
        }
        if (empty($data['card_pin'])) {
            unset($data['card_pin']);
        }
        if (empty($data['card_number'])) {
            unset($data['card_number']);
        }

        $digitalCard->update($data);

        return redirect()->route('dashboard.digital-cards.index')
            ->with('success', 'تم تحديث البطاقة الرقمية بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalCard $digitalCard)
    {
        $digitalCard->delete();

        return redirect()->route('dashboard.digital-cards.index')
            ->with('success', 'تم حذف البطاقة الرقمية بنجاح');
    }

    /**
     * Toggle card status.
     */
    public function toggleStatus(DigitalCard $digitalCard)
    {
        $newStatus = $digitalCard->status === 'active' ? 'inactive' : 'active';
        $digitalCard->update(['status' => $newStatus]);

        $status = $newStatus === 'active' ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} البطاقة بنجاح");
    }

    /**
     * Mark card as used.
     */
    public function markAsUsed(Request $request, DigitalCard $digitalCard)
    {
        $request->validate([
            'used_by' => 'required|exists:users,id',
            'order_item_id' => 'nullable|exists:order_items,id',
        ]);

        $digitalCard->markAsUsed($request->used_by, $request->order_item_id);

        return redirect()->back()
            ->with('success', 'تم تمييز البطاقة كمستخدمة بنجاح');
    }

    /**
     * Generate bulk cards for a product.
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:1000',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        // إنشاء البطاقات
        $createdCards = [];
        for ($i = 0; $i < $quantity; $i++) {
            $card = DigitalCard::create([
                'product_id' => $product->id,
                'card_code' => $this->generateCardCode(),
                'card_pin' => $this->generateCardPin(),
                'card_number' => $this->generateCardNumber(),
                'serial_number' => $this->generateSerialNumber(),
                'value' => $request->value,
                'currency' => $request->currency,
                'expiry_date' => $request->expiry_date,
                'status' => 'active',
            ]);
            $createdCards[] = $card;
        }

        return redirect()->route('dashboard.digital-cards.index')
            ->with('success', "تم إنشاء {$quantity} بطاقة رقمية للمنتج {$product->name} بنجاح");
    }

    /**
     * Export digital cards to CSV.
     */
    public function export()
    {
        $digitalCards = DigitalCard::with(['product', 'usedBy'])->get();

        $filename = 'digital_cards_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($digitalCards) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'رمز البطاقة',
                'PIN',
                'رقم البطاقة',
                'الرقم التسلسلي',
                'المنتج',
                'القيمة',
                'العملة',
                'تاريخ الانتهاء',
                'الحالة',
                'مستخدم',
                'تاريخ الاستخدام',
                'تاريخ الإنشاء'
            ]);

            foreach ($digitalCards as $card) {
                fputcsv($file, [
                    $card->card_code,
                    $card->card_pin,
                    $card->card_number,
                    $card->serial_number,
                    $card->product->name,
                    $card->value,
                    $card->currency,
                    $card->expiry_date ? $card->expiry_date->format('Y-m-d') : '',
                    $card->status,
                    $card->usedBy ? $card->usedBy->name : '',
                    $card->used_at ? $card->used_at->format('Y-m-d H:i:s') : '',
                    $card->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate unique card code.
     */
    private function generateCardCode(): string
    {
        do {
            $code = strtoupper(Str::random(16));
        } while (DigitalCard::where('card_code', $code)->exists());

        return $code;
    }

    /**
     * Generate unique card PIN.
     */
    private function generateCardPin(): string
    {
        do {
            $pin = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (DigitalCard::where('card_pin', $pin)->exists());

        return $pin;
    }

    /**
     * Generate unique card number.
     */
    private function generateCardNumber(): string
    {
        do {
            $number = str_pad(random_int(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
        } while (DigitalCard::where('card_number', $number)->exists());

        return $number;
    }

    /**
     * Generate unique serial number.
     */
    private function generateSerialNumber(): string
    {
        do {
            $serial = strtoupper(Str::random(12));
        } while (DigitalCard::where('serial_number', $serial)->exists());

        return $serial;
    }
}
