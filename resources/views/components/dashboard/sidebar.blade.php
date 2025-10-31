{{-- مكون الشريط الجانبي المحسن --}}
<nav class="sidebar open" id="sidebar">
    {{-- رأس الشريط الجانبي --}}
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard.index') }}" class="brand-link">
                <div class="brand-icon-wrapper">
                    <i class="bi bi-wallet2 brand-icon"></i>
                </div>
                <div class="brand-content">
                    <span class="brand-text">متجر البطاقات الرقمية</span>
                    <span class="brand-subtitle">لوحة التحكم</span>
                </div>
            </a>
        </div>
        <button class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn">
            <i class="bi bi-x"></i>
        </button>
    </div>

    {{-- زر طي الشريط الجانبي --}}
    <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="طي الشريط الجانبي">
        <i class="bi bi-chevron-right"></i>
    </button>

    {{-- محتوى الشريط الجانبي --}}
    <div class="sidebar-content">
        {{-- لوحة التحكم الرئيسية --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">الرئيسية</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" href="{{ route('dashboard.index') }}" title="لوحة التحكم الرئيسية">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-house nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">لوحة التحكم الرئيسية</span>
                            <span class="nav-subtitle">نظرة عامة</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        {{-- إدارة البطاقات الرقمية --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">البطاقات الرقمية</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.categories*') ? 'active' : '' }}" href="{{ route('dashboard.categories.index') }}" title="تصنيفات البطاقات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-collection nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">تصنيفات البطاقات</span>
                            <span class="nav-subtitle">إدارة تصنيفات البطاقات</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.products*') ? 'active' : '' }}" href="{{ route('dashboard.products.index') }}" title="أنواع البطاقات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-grid-3x3-gap nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">أنواع البطاقات</span>
                            <span class="nav-subtitle">إدارة أنواع البطاقات</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.digital-cards*') ? 'active' : '' }}" href="{{ route('dashboard.digital-cards.index') }}" title="البطاقات المتاحة">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-wallet2 nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">البطاقات المتاحة</span>
                            <span class="nav-subtitle">إدارة البطاقات المتاحة</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        {{-- المبيعات والعروض --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">المبيعات والعروض</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.orders*') ? 'active' : '' }}" href="{{ route('dashboard.orders.index') }}" title="إدارة الطلبات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-receipt nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إدارة الطلبات</span>
                            <span class="nav-subtitle">عرض وإدارة الطلبات</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.carts*') ? 'active' : '' }}" href="{{ route('dashboard.carts.index') }}" title="السلات المتروكة">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-cart-x nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">السلات المتروكة</span>
                            <span class="nav-subtitle">إدارة السلات المتروكة</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.coupons*') ? 'active' : '' }}" href="{{ route('dashboard.coupons.index') }}" title="كوبونات الخصم">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-percent nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">كوبونات الخصم</span>
                            <span class="nav-subtitle">إدارة كوبونات الخصم</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.sliders*') ? 'active' : '' }}" href="{{ route('dashboard.sliders.index') }}" title="إدارة السلايدرات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-images nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إدارة السلايدرات</span>
                            <span class="nav-subtitle">العروض الترويجية</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        {{-- العملاء والمكافآت --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">العملاء والمكافآت</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.users*') ? 'active' : '' }}" href="{{ route('dashboard.users.index') }}" title="إدارة العملاء">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-person-check nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إدارة العملاء</span>
                            <span class="nav-subtitle">إدارة العملاء والمستخدمين</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.loyalty-points*') ? 'active' : '' }}" href="{{ route('dashboard.loyalty-points.index') }}" title="نظام النقاط">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-gem nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">نظام النقاط</span>
                            <span class="nav-subtitle">إدارة نظام نقاط الولاء</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- <a class="nav-link {{ request()->routeIs('dashboard.loyalty-settings*') ? 'active' : '' }}" href="{{ route('dashboard.loyalty-settings.index') }}" title="إعدادات الولاء">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-gear nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إعدادات الولاء</span>
                            <span class="nav-subtitle">إعدادات نظام نقاط الولاء</span>
                        </div>
                    </a> --}}
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.referrals*') ? 'active' : '' }}" href="{{ route('dashboard.referrals.index') }}" title="نظام الإحالات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-share nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">نظام الإحالات</span>
                            <span class="nav-subtitle">إدارة نظام الإحالات</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        {{-- التواصل والإشعارات --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">التواصل والإشعارات</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.notifications*') ? 'active' : '' }}" href="{{ route('dashboard.notifications.index') }}" title="إدارة الإشعارات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-megaphone nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إدارة الإشعارات</span>
                            <span class="nav-subtitle">إدارة نظام الإشعارات</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.contacts*') ? 'active' : '' }}" href="{{ route('dashboard.contacts.index') }}" title="رسائل العملاء">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-envelope nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">رسائل العملاء</span>
                            <span class="nav-subtitle">إدارة رسائل العملاء</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.email*') ? 'active' : '' }}" href="{{ route('dashboard.email.index') }}" title="إرسال البريد الإلكتروني">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-send nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إرسال البريد الإلكتروني</span>
                            <span class="nav-subtitle">إرسال رسائل بريد إلكتروني</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        {{-- إدارة الأدوار والصلاحيات --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">الأدوار والصلاحيات</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.roles*') ? 'active' : '' }}" href="{{ route('dashboard.roles.index') }}" title="إدارة الأدوار">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-person-badge nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إدارة الأدوار</span>
                            <span class="nav-subtitle">إدارة أدوار المستخدمين</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.permissions*') ? 'active' : '' }}" href="{{ route('dashboard.permissions.index') }}" title="إدارة الصلاحيات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-unlock nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إدارة الصلاحيات</span>
                            <span class="nav-subtitle">إدارة صلاحيات النظام</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        {{-- التقارير والإعدادات --}}
        <div class="sidebar-section">
            <div class="section-title">
                <span class="section-text">التقارير والإعدادات</span>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.reports*') ? 'active' : '' }}" href="{{ route('dashboard.reports') }}" title="التقارير والإحصائيات">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-bar-chart nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">التقارير والإحصائيات</span>
                            <span class="nav-subtitle">تقارير شاملة عن الأداء</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.settings*') ? 'active' : '' }}" href="{{ route('dashboard.settings.index') }}" title="إعدادات النظام">
                        <div class="nav-icon-wrapper">
                            <i class="bi bi-sliders nav-icon"></i>
                        </div>
                        <div class="nav-content">
                            <span class="nav-text">إعدادات النظام</span>
                            <span class="nav-subtitle">إعدادات النظام</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Overlay للشاشات الصغيرة --}}
<div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>
