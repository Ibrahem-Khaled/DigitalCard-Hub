<!DOCTYPE html>
<html lang="{{ $settings['default_language'] ?? 'ar' }}" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings['meta_title'] ?? ($settings['site_name'] ?? 'متجر البطاقات الرقمية'))</title>

    <!-- Meta Tags -->
    <meta name="description" content="@yield('description', $settings['meta_description'] ?? ($settings['site_description'] ?? ''))">
    <meta name="keywords" content="{{ $settings['site_keywords'] ?? '' }}">
    <meta name="author" content="{{ $settings['site_name'] ?? 'متجر البطاقات الرقمية' }}">

    <!-- Favicon -->
    @if (!empty($settings['site_favicon']))
        <link rel="icon" type="image/png" href="{{ $settings['site_favicon'] }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php
        $fontFamily = $settings['font_family'] ?? 'Cairo';
    @endphp
    <link
        href="https://fonts.googleapis.com/css2?family={{ $fontFamily }}:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <!-- Analytics -->
    @if (!empty($settings['google_analytics']))
        {!! $settings['google_analytics'] !!}
    @endif
</head>

<body class="bg-[#0F0F0F] text-gray-100 min-h-screen">
    <!-- Navigation -->
    @include('components.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- AI Chatbot -->
    @include('components.ai-chatbot')

    <!-- Scripts -->
    @stack('scripts')

    <!-- AI Chatbot Script -->
    <script src="{{ asset('assets/js/ai-chatbot.js') }}"></script>
</body>

</html>
