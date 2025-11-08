@extends('layouts.app')

@section('title', 'سلة التسوق')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">
                <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">سلة التسوق</span>
            </h1>
            <p class="text-gray-400">مراجعة المنتجات قبل إتمام الشراء</p>
        </div>

        @if($cart->items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6 hover:border-purple-500/50 transition-all">
                    <div class="flex gap-6">
                        <!-- Product Image -->
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-[#0F0F0F] flex-shrink-0">
                            @if($item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-bold text-white mb-1">{{ $item->product->name }}</h3>
                                    @if($item->product->category)
                                        <p class="text-sm text-gray-400">{{ $item->product->category->name }}</p>
                                    @endif
                                </div>
                                <button onclick="removeFromCart({{ $item->id }})" class="text-red-500 hover:text-red-400 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <!-- Quantity -->
                                <div class="flex items-center gap-3">
                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="w-8 h-8 rounded-lg bg-[#0F0F0F] border border-purple-500/20 flex items-center justify-center text-white hover:border-purple-500 transition-all"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span class="text-white font-bold w-8 text-center">{{ $item->quantity }}</span>
                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            class="w-8 h-8 rounded-lg bg-[#0F0F0F] border border-purple-500/20 flex items-center justify-center text-white hover:border-purple-500 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Price -->
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-white">
                                        {{ formatPrice($item->total_price) }}
                                    </p>
                                    <p class="text-sm text-gray-400">{{ formatPrice($item->price) }} × {{ $item->quantity }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Coupon -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h3 class="text-lg font-bold text-white mb-4">كود الخصم</h3>

                    @if($cart->coupon_code)
                        <!-- Applied Coupon -->
                        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-green-400 font-semibold">كود الخصم مطبق</span>
                                    </div>
                                    <p class="text-white font-bold">{{ $cart->coupon_code }}</p>
                                    <p class="text-sm text-gray-400">خصم: {{ formatPrice($cart->discount_amount) }}</p>
                                </div>
                                <button onclick="removeCoupon()" class="text-red-400 hover:text-red-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Coupon Form -->
                        <form id="coupon-form" class="flex gap-3">
                            @csrf
                            <input type="text" name="coupon_code" placeholder="أدخل كود الخصم"
                                   class="flex-1 px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all">
                                تطبيق
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6 sticky top-24">
                    <h3 class="text-2xl font-bold text-white mb-6">ملخص الطلب</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-300">
                            <span>المجموع الفرعي</span>
                            <span class="font-semibold">{{ formatPrice($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>الضريبة (14%)</span>
                            <span class="font-semibold">{{ formatPrice($tax) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-green-400">
                            <span>الخصم</span>
                            <span class="font-semibold">- {{ formatPrice($discount) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-gray-300">
                            <span>الشحن</span>
                            <span class="font-semibold text-green-400">مجاني</span>
                        </div>
                    </div>

                    <div class="border-t border-purple-500/20 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-white">الإجمالي</span>
                            <span class="text-3xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                                {{ formatPrice($total) }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-center hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105 mb-3">
                        إتمام الشراء
                    </a>

                    <a href="{{ route('products.index') }}" class="block w-full bg-[#0F0F0F] border border-purple-500/20 text-white py-4 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                        متابعة التسوق
                    </a>

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t border-purple-500/20">
                        <div class="flex items-center gap-2 text-gray-400 text-sm mb-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>دفع آمن ومشفر</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm mb-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>تسليم فوري للمنتجات</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <span>إمكانية الاسترجاع خلال 24 ساعة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="max-w-2xl mx-auto text-center py-20">
            <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-r from-purple-500/10 to-orange-500/10 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-4">سلة التسوق فارغة</h2>
            <p class="text-gray-400 mb-8">لم تقم بإضافة أي منتجات إلى سلة التسوق بعد</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-gradient-to-r from-purple-500 to-orange-500 text-white px-8 py-4 rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                تصفح المنتجات
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateQuantity(itemId, quantity) {
    if (quantity < 1) return;

    fetch(`/cart/${itemId}/update`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function removeFromCart(itemId) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) return;

    fetch(`/cart/${itemId}/remove`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Coupon form
document.getElementById('coupon-form')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/cart/apply-coupon', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'كود الخصم غير صحيح');
        }
    })
    .catch(error => console.error('Error:', error));
});

// Remove coupon function
function removeCoupon() {
    if (!confirm('هل أنت متأكد من إزالة كود الخصم؟')) return;

    fetch('/cart/remove-coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء إزالة كود الخصم');
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

@endpush
@endsection

