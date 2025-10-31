<div class="group relative bg-[#1F1F1F] rounded-2xl overflow-hidden border border-purple-500/20 hover:border-purple-500 transition-all duration-300 card-hover">
    <!-- Product Image -->
    <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-square">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-purple-500/20 to-orange-500/20 flex items-center justify-center">
                <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        <!-- Badges -->
        <div class="absolute top-3 right-3 flex flex-col gap-2">
            @if($product->isOnSale())
                <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    خصم {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                </span>
            @endif

            @if($product->is_featured)
                <span class="bg-gradient-to-r from-purple-500 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    مميز
                </span>
            @endif

            @if(isset($badge))
                <span class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    {{ $badge }}
                </span>
            @endif

            @if($product->is_instant_delivery)
                <span class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    ⚡ فوري
                </span>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button onclick="addToCart({{ $product->id }})"
                    class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                أضف للسلة
            </button>
        </div>
    </a>

    <!-- Product Info -->
    <div class="p-5">
        <!-- Category -->
        @if($product->category)
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
               class="inline-block text-xs text-purple-400 hover:text-orange-400 transition-colors mb-2 font-semibold">
                {{ $product->category->name }}
            </a>
        @endif

        <!-- Product Name -->
        <a href="{{ route('products.show', $product->slug) }}"
           class="block text-lg font-bold text-white hover:text-purple-400 transition-colors mb-3 line-clamp-2">
            {{ $product->name }}
        </a>

        <!-- Rating & Reviews -->
        <div class="flex items-center gap-2 mb-3">
            <div class="flex items-center">
                @php
                    $avgRating = $product->reviews()->avg('rating') ?? 0;
                    $fullStars = floor($avgRating);
                    $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                @endphp

                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $fullStars)
                        <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @elseif($hasHalfStar && $i == $fullStars + 1)
                        <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="half-star-{{ $product->id }}">
                                    <stop offset="50%" stop-color="currentColor"/>
                                    <stop offset="50%" stop-color="#4B5563"/>
                                </linearGradient>
                            </defs>
                            <path fill="url(#half-star-{{ $product->id }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endif
                @endfor
            </div>
            <span class="text-xs text-gray-400">({{ $product->reviews()->count() }})</span>
        </div>

        <!-- Price -->
        <div class="flex items-center justify-between">
            <div>
                @if($product->isOnSale())
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black text-white">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                    </div>
                @else
                    <span class="text-2xl font-black text-white">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            <!-- Wishlist Button -->
            <button onclick="toggleWishlist({{ $product->id }})"
                    class="w-10 h-10 rounded-full bg-white/5 hover:bg-purple-500/20 flex items-center justify-center transition-all duration-300 group/wishlist">
                <svg class="w-5 h-5 text-gray-400 group-hover/wishlist:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        <!-- Loyalty Points -->
        @if($product->loyalty_points_earn > 0)
            <div class="mt-3 flex items-center gap-2 text-xs text-purple-400">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>اكسب {{ $product->loyalty_points_earn }} نقطة</span>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function addToCart(productId) {
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Update cart count
            if (window.updateCartCount) {
                window.updateCartCount(data.cart_count);
            }

            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('cart-updated', {
                detail: { count: data.cart_count }
            }));

            // Show success message
            showNotification(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء إضافة المنتج للسلة', 'error');
    });
}

function toggleWishlist(productId) {
    // Implement wishlist functionality
    console.log('Toggle wishlist for product:', productId);
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-24 left-1/2 -translate-x-1/2 z-50 px-6 py-4 rounded-xl shadow-2xl animate-fade-in-down ${
        type === 'success' ? 'bg-green-500/90 text-white' : 'bg-red-500/90 text-white'
    } backdrop-blur-lg border ${
        type === 'success' ? 'border-green-400/30' : 'border-red-400/30'
    }`;

    notification.innerHTML = `
        <div class="flex items-center gap-3">
            ${type === 'success' ?
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
            }
            <span class="font-semibold">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'fade-out-up 0.3s ease-out forwards';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fade-in-down {
        from {
            opacity: 0;
            transform: translate(-50%, -20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }
    @keyframes fade-out-up {
        from {
            opacity: 1;
            transform: translate(-50%, 0);
        }
        to {
            opacity: 0;
            transform: translate(-50%, -20px);
        }
    }
    .animate-fade-in-down {
        animation: fade-in-down 0.3s ease-out forwards;
    }
`;
document.head.appendChild(style);
</script>
@endpush

