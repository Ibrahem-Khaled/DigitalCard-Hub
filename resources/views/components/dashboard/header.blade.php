{{-- مكون الهيدر --}}
<header class="header">
    <div class="header-container">
        {{-- الجانب الأيسر --}}
        <div class="header-left">
            <button class="sidebar-toggle-btn d-lg-none" id="sidebarToggleBtn">
                <i class="bi bi-list"></i>
            </button>
            <button class="sidebar-expand-btn d-none d-lg-block" id="sidebarExpandBtn">
                <i class="bi bi-list"></i>
            </button>
            <div class="page-info">
                <h1 class="page-title">@yield('page-title', 'لوحة التحكم')</h1>
                <p class="page-subtitle">@yield('page-subtitle', 'مرحباً بك في لوحة التحكم')</p>
            </div>
        </div>

        {{-- الجانب الأيمن --}}
        <div class="header-right">
            {{-- شريط البحث --}}
            {{-- <div class="search-container">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="البحث...">
                    <button class="search-btn">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                </div>
            </div> --}}

            {{-- الإشعارات --}}
            {{-- <div class="notifications-container">
                <div class="dropdown notifications-dropdown">
                    <button class="notification-btn" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-menu">
                        <div class="notification-header">
                            <h6 class="notification-title">الإشعارات</h6>
                            <button class="mark-all-read">تعيين الكل كمقروء</button>
                        </div>
                        <div class="notification-list">
                            <div class="notification-item unread">
                                <div class="notification-icon">
                                    <i class="bi bi-cart-plus"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">طلب جديد #1234</div>
                                    <div class="notification-desc">تم إنشاء طلب جديد من العميل أحمد محمد</div>
                                    <div class="notification-time">منذ 5 دقائق</div>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div class="notification-icon">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">دفعة جديدة</div>
                                    <div class="notification-desc">تم استلام دفعة بقيمة 150.00 $</div>
                                    <div class="notification-time">منذ 15 دقيقة</div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="bi bi-gear"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">تحديث النظام</div>
                                    <div class="notification-desc">تم تحديث النظام بنجاح</div>
                                    <div class="notification-time">منذ ساعة</div>
                                </div>
                            </div>
                        </div>
                        <div class="notification-footer">
                            <a href="#" class="view-all-notifications">عرض جميع الإشعارات</a>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- قائمة المستخدم --}}
            <div class="user-container">
                <div class="dropdown user-dropdown">
                    <button class="user-btn" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ auth()->user()->display_name ?? 'م' }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->full_name ?? 'المستخدم' }}</div>
                            <div class="user-role">{{ auth()->user()->isAdmin() ? 'مدير' : 'مستخدم' }}</div>
                        </div>
                        <i class="bi bi-chevron-down user-arrow"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end user-menu">
                        <div class="user-menu-body">
                            <a class="user-menu-item" href="#">
                                <i class="bi bi-person"></i>
                                <span>الملف الشخصي</span>
                            </a>
                            <a class="user-menu-item" href="#">
                                <i class="bi bi-gear"></i>
                                <span>الإعدادات</span>
                            </a>
                            <a class="user-menu-item" href="{{ route('password.change') }}">
                                <i class="bi bi-key"></i>
                                <span>تغيير كلمة المرور</span>
                            </a>
                            <div class="user-menu-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-menu-item logout-btn">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>تسجيل الخروج</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
