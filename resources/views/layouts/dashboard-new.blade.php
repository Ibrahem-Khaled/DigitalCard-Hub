<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم - متجر البطاقات الرقمية')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts - Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS Files -->
    <link href="{{ asset('assets/styles/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/styles/enhanced-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/styles/professional-sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/styles/sidebar-collapsed-fix.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/styles/simplified-header.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="app-container">
        {{-- الشريط الجانبي --}}
        <x-dashboard.sidebar />

        {{-- المحتوى الرئيسي --}}
        <div class="main-wrapper">
            {{-- الهيدر --}}
            <x-dashboard.header />

            {{-- محتوى الصفحة --}}
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

    @stack('scripts')
</body>
</html>
