<nav style="z-index: 100;" class="sticky top-0 bg-[#1A1A1A]/95 backdrop-blur-lg border-b border-purple-500/20">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @if(!empty($settings['site_logo']))
                        <img src="{{ $settings['site_logo'] }}" alt="{{ $settings['site_name'] ?? 'متجر البطاقات الرقمية' }}" class="h-12 w-auto">
                    @else
                        <span class="text-2xl font-bold bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                            {{ $settings['site_name'] ?? 'متجر البطاقات الرقمية' }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-xl mx-8">
                <form action="{{ route('products.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               placeholder="ابحث عن المنتجات..."
                               class="w-full bg-[#0F0F0F] border border-purple-500/30 rounded-full px-6 py-3 pr-12 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                        <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-purple-500 hover:text-orange-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Navigation Links -->
            <div class="hidden lg:flex items-center gap-8">
                <!-- Currency Selector -->
                <div class="relative group">
                    <button class="flex items-center gap-2 text-gray-300 hover:text-purple-500 transition-colors font-medium px-3 py-2 rounded-lg hover:bg-purple-500/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ getCurrencySymbol(session('currency', getUserCurrency())) }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Currency Dropdown -->
                    <div class="absolute left-0 mt-2 w-48 bg-[#1F1F1F] border border-purple-500/20 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[110]">
                        <div class="py-2">
                            @php
                                $currencies = [
                                    'USD' => ['name' => 'دولار أمريكي', 'symbol' => '$'],
                                    'OMR' => ['name' => 'ريال عماني', 'symbol' => 'ر.ع.'],
                                    'SAR' => ['name' => 'ريال سعودي', 'symbol' => 'ر.س'],
                                    'AED' => ['name' => 'درهم إماراتي', 'symbol' => 'د.إ'],
                                    'EGP' => ['name' => 'جنيه مصري', 'symbol' => 'ج.م'],
                                    'KWD' => ['name' => 'دينار كويتي', 'symbol' => 'د.ك'],
                                    'QAR' => ['name' => 'ريال قطري', 'symbol' => 'ر.ق'],
                                    'BHD' => ['name' => 'دينار بحريني', 'symbol' => 'د.ب'],
                                    'EUR' => ['name' => 'يورو', 'symbol' => '€'],
                                ];
                                $currentCurrency = session('currency', getUserCurrency());
                            @endphp
                            @foreach($currencies as $code => $currency)
                            <form action="{{ route('currency.change') }}" method="POST" class="inline-block w-full">
                                @csrf
                                <input type="hidden" name="currency" value="{{ $code }}">
                                <button type="submit" class="w-full text-right px-4 py-2 text-gray-300 hover:bg-purple-500/10 hover:text-purple-500 transition-colors flex items-center justify-between {{ $currentCurrency === $code ? 'bg-purple-500/20 text-purple-400' : '' }}">
                                    <span>{{ $currency['name'] }}</span>
                                    <span class="text-sm font-semibold">{{ $currency['symbol'] }}</span>
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="text-gray-300 hover:text-purple-500 transition-colors font-medium">
                    الرئيسية
                </a>
                <a href="{{ route('products.index') }}" class="text-gray-300 hover:text-purple-500 transition-colors font-medium">
                    المنتجات
                </a>
                <a href="{{ route('cart.index') }}" class="relative text-gray-300 hover:text-purple-500 transition-colors font-medium">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @php
                        $cartCount = 0;
                        try {
                            if (auth()->check()) {
                                $cart = \App\Models\Cart::with('items')->where('user_id', auth()->id())->first();
                            } else {
                                $sessionId = session()->getId();
                                if ($sessionId) {
                                    $cart = \App\Models\Cart::with('items')->where('session_id', $sessionId)->first();
                                }
                            }
                            if (isset($cart) && $cart) {
                                $cartCount = $cart->items->sum('quantity');
                            }
                        } catch (\Exception $e) {
                            $cartCount = 0;
                        }
                    @endphp
                    @if($cartCount > 0)
                    <span id="cart-count" class="absolute -top-2 -left-2 bg-gradient-to-r from-purple-500 to-orange-500 text-white text-xs rounded-full w-5 h-5 px-1 flex items-center justify-center font-bold">
                        {{ $cartCount }}
                    </span>
                    @endif
                </a>

                @auth
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-gray-300 hover:text-purple-500 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold">
                                {{ auth()->user()->first_name[0] }}
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute left-0 mt-2 w-56 bg-[#1F1F1F] border border-purple-500/20 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[110]">
                            <div class="p-4 border-b border-purple-500/20">
                                <p class="font-semibold text-white">{{ auth()->user()->full_name }}</p>
                                <p class="text-sm text-gray-400">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-2">
                                @if(auth()->user()->hasRole(['admin', 'super_admin', 'manager']))
                                <a href="{{ route('dashboard.index') }}" class="block px-4 py-2 text-orange-400 hover:bg-orange-500/10 hover:text-orange-500 transition-colors font-semibold">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        <span>لوحة التحكم</span>
                                    </div>
                                </a>
                                <div class="border-t border-purple-500/10 my-2"></div>
                                @endif
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-purple-500/10 hover:text-purple-500 transition-colors">
                                    الملف الشخصي
                                </a>
                                <a href="{{ route('profile.orders') }}" class="block px-4 py-2 text-gray-300 hover:bg-purple-500/10 hover:text-purple-500 transition-colors">
                                    طلباتي
                                </a>
                                <a href="{{ route('profile.loyalty-points') }}" class="block px-4 py-2 text-gray-300 hover:bg-purple-500/10 hover:text-purple-500 transition-colors">
                                    نقاط الولاء
                                </a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-right px-4 py-2 text-red-400 hover:bg-red-500/10 transition-colors">
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300">
                        تسجيل الدخول
                    </a>
                @endauth
            </div>

            <!-- Mobile Actions (Cart, Currency, Menu) -->
            <div class="flex items-center gap-3 lg:hidden">
                <!-- Currency Selector (Mobile) -->
                <div class="relative">
                    <button id="mobile-currency-btn" class="flex items-center gap-1 text-gray-300 hover:text-purple-500 transition-colors p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold">{{ getCurrencySymbol(session('currency', getUserCurrency())) }}</span>
                    </button>
                    
                    <!-- Currency Dropdown (Mobile) -->
                    <div id="mobile-currency-dropdown" class="hidden absolute left-0 mt-2 w-48 bg-[#1F1F1A] border border-purple-500/20 rounded-lg shadow-xl z-[110]">
                        <div class="py-2">
                            @php
                                $currencies = [
                                    'USD' => ['name' => 'دولار أمريكي', 'symbol' => '$'],
                                    'OMR' => ['name' => 'ريال عماني', 'symbol' => 'ر.ع.'],
                                    'SAR' => ['name' => 'ريال سعودي', 'symbol' => 'ر.س'],
                                    'AED' => ['name' => 'درهم إماراتي', 'symbol' => 'د.إ'],
                                    'EGP' => ['name' => 'جنيه مصري', 'symbol' => 'ج.م'],
                                    'KWD' => ['name' => 'دينار كويتي', 'symbol' => 'د.ك'],
                                    'QAR' => ['name' => 'ريال قطري', 'symbol' => 'ر.ق'],
                                    'BHD' => ['name' => 'دينار بحريني', 'symbol' => 'د.ب'],
                                    'EUR' => ['name' => 'يورو', 'symbol' => '€'],
                                ];
                                $currentCurrency = session('currency', getUserCurrency());
                            @endphp
                            @foreach($currencies as $code => $currency)
                            <form action="{{ route('currency.change') }}" method="POST" class="inline-block w-full">
                                @csrf
                                <input type="hidden" name="currency" value="{{ $code }}">
                                <button type="submit" class="w-full text-right px-4 py-2 text-gray-300 hover:bg-purple-500/10 hover:text-purple-500 transition-colors flex items-center justify-between {{ $currentCurrency === $code ? 'bg-purple-500/20 text-purple-400' : '' }}">
                                    <span class="text-sm">{{ $currency['name'] }}</span>
                                    <span class="text-xs font-semibold">{{ $currency['symbol'] }}</span>
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart (Mobile) -->
                <a href="{{ route('cart.index') }}" class="relative text-gray-300 hover:text-purple-500 transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if($cartCount > 0)
                    <span id="cart-count-mobile" class="absolute -top-1 -left-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                        {{ $cartCount }}
                    </span>
                    @endif
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="text-gray-300 hover:text-purple-500 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden border-t border-purple-500/20 relative z-[100]">
        <div class="container mx-auto px-4 py-4 space-y-4">
            <form action="{{ route('products.index') }}" method="GET" class="md:hidden">
                <div class="relative">
                    <input type="text"
                           name="search"
                           placeholder="ابحث عن المنتجات..."
                           class="w-full bg-[#0F0F0F] border border-purple-500/30 rounded-full px-6 py-3 pr-12 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-purple-500">
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-purple-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            <a href="{{ route('home') }}" class="block text-gray-300 hover:text-purple-500 transition-colors font-medium">
                الرئيسية
            </a>
            <a href="{{ route('products.index') }}" class="block text-gray-300 hover:text-purple-500 transition-colors font-medium">
                المنتجات
            </a>

            @auth
                @if(auth()->user()->hasRole(['admin', 'super_admin', 'manager']))
                <a href="{{ route('dashboard.index') }}" class="block text-orange-400 hover:text-orange-500 transition-colors font-bold">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>لوحة التحكم</span>
                    </div>
                </a>
                <div class="border-t border-purple-500/20 my-2"></div>
                @endif
                <a href="{{ route('profile.index') }}" class="block text-gray-300 hover:text-purple-500 transition-colors font-medium">
                    الملف الشخصي
                </a>
                <a href="{{ route('profile.orders') }}" class="block text-gray-300 hover:text-purple-500 transition-colors font-medium">
                    طلباتي
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors font-medium">
                        تسجيل الخروج
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-6 py-2 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-semibold text-center">
                    تسجيل الدخول
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
    // Mobile Menu Toggle
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    // Mobile Currency Dropdown Toggle
    const mobileCurrencyBtn = document.getElementById('mobile-currency-btn');
    const mobileCurrencyDropdown = document.getElementById('mobile-currency-dropdown');
    
    if (mobileCurrencyBtn && mobileCurrencyDropdown) {
        mobileCurrencyBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileCurrencyDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileCurrencyBtn.contains(e.target) && !mobileCurrencyDropdown.contains(e.target)) {
                mobileCurrencyDropdown.classList.add('hidden');
            }
        });
    }

    // Update cart count dynamically
    window.updateCartCount = function(count) {
        // Update desktop cart count
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            if (count > 0) {
                cartCountElement.textContent = count;
                cartCountElement.classList.remove('hidden');
            } else {
                cartCountElement.classList.add('hidden');
            }
        }
        
        // Update mobile cart count
        const cartCountMobile = document.getElementById('cart-count-mobile');
        if (cartCountMobile) {
            if (count > 0) {
                cartCountMobile.textContent = count;
                cartCountMobile.classList.remove('hidden');
            } else {
                cartCountMobile.classList.add('hidden');
            }
        }
    };

    // Listen for cart updates (can be triggered from product pages)
    window.addEventListener('cart-updated', function(e) {
        if (e.detail && e.detail.count !== undefined) {
            window.updateCartCount(e.detail.count);
        }
    });
</script>

