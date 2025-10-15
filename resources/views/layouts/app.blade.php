<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'متجر البطاقات الرقمية')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts - Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #8B5CF6;
            --secondary-purple: #A78BFA;
            --light-purple: #DDD6FE;
            --dark-purple: #6D28D9;
            --accent-purple: #C4B5FD;
            --text-dark: #1F2937;
            --text-light: #6B7280;
            --bg-light: #F9FAFB;
            --bg-white: #FFFFFF;
            --border-color: #E5E7EB;
        }

        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 14px;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(139, 92, 246, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(-2px);
        }

        .sidebar .nav-link i {
            margin-left: 10px;
            font-size: 16px;
        }

        .main-content {
            background-color: var(--bg-white);
            min-height: 100vh;
            border-radius: 20px 0 0 20px;
            margin-right: 0;
            box-shadow: -2px 0 20px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: linear-gradient(135deg, var(--bg-white) 0%, var(--light-purple) 100%);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 30px;
            border-radius: 20px 0 0 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--bg-white) 0%, var(--light-purple) 100%);
            border-left: 4px solid var(--primary-purple);
        }

        .stats-card .card-body {
            padding: 25px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-purple);
            margin-bottom: 5px;
        }

        .stats-label {
            color: var(--text-light);
            font-weight: 500;
            font-size: 14px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-purple) !important;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--light-purple);
            color: var(--primary-purple);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--light-purple);
            border: none;
            color: var(--text-dark);
            font-weight: 600;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            border-color: var(--border-color);
        }

        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-weight: 500;
        }

        .badge-success {
            background-color: #10B981;
        }

        .badge-warning {
            background-color: #F59E0B;
        }

        .badge-danger {
            background-color: #EF4444;
        }

        .badge-info {
            background-color: var(--primary-purple);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                right: -100%;
                width: 280px;
                z-index: 1050;
                transition: all 0.3s ease;
            }

            .sidebar.show {
                right: 0;
            }

            .main-content {
                border-radius: 0;
                margin-right: 0;
            }

            .header {
                border-radius: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="bi bi-shop"></i>
                        متجر البطاقات
                    </h4>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                لوحة التحكم
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.products*') ? 'active' : '' }}" href="{{ route('dashboard.products') }}">
                                <i class="bi bi-box-seam"></i>
                                المنتجات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.orders*') ? 'active' : '' }}" href="{{ route('dashboard.orders') }}">
                                <i class="bi bi-cart-check"></i>
                                الطلبات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.customers*') ? 'active' : '' }}" href="{{ route('dashboard.customers') }}">
                                <i class="bi bi-people"></i>
                                العملاء
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.categories*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-tags"></i>
                                الفئات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.reports*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-graph-up"></i>
                                التقارير
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.settings*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-gear"></i>
                                الإعدادات
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mt-auto p-4">
                    <div class="text-center">
                        <div class="user-avatar mx-auto mb-2">
                            {{ substr(auth()->user()->name ?? 'م', 0, 1) }}
                        </div>
                        <small class="text-white-50">{{ auth()->user()->name ?? 'المستخدم' }}</small>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <header class="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-primary d-md-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                                <i class="bi bi-list"></i>
                            </button>
                            <h2 class="mb-0">@yield('page-title', 'لوحة التحكم')</h2>
                        </div>

                        <div class="d-flex align-items-center">
                            <!-- Notifications -->
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-primary position-relative" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        3
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><h6 class="dropdown-header">الإشعارات</h6></li>
                                    <li><a class="dropdown-item" href="#">طلب جديد #1234</a></li>
                                    <li><a class="dropdown-item" href="#">دفعة جديدة</a></li>
                                    <li><a class="dropdown-item" href="#">تحديث النظام</a></li>
                                </ul>
                            </div>

                            <!-- User Menu -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                    <div class="user-avatar me-2">
                                        {{ substr(auth()->user()->name ?? 'م', 0, 1) }}
                                    </div>
                                    {{ auth()->user()->name ?? 'المستخدم' }}
                                    <i class="bi bi-chevron-down me-2"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>الإعدادات</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="p-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
