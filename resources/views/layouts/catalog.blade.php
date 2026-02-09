<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>@yield('title', 'الكورسات') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Cairo', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- ناف بار -->
    <header class="bg-gradient-to-l from-indigo-700 to-purple-800 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-white font-bold text-xl hover:opacity-90">
                    <i class="fas fa-calculator"></i>
                    <span>منصة الطارق</span>
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ url('/') }}" class="text-white/90 hover:text-white font-medium">الرئيسية</a>
                    <a href="{{ route('catalog.index') }}" class="text-white font-semibold border-b-2 border-white pb-0.5">كورسات</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-tachometer-alt ml-2"></i>
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-white/90 hover:text-white font-medium">دخول</a>
                        <a href="{{ route('register') }}" class="bg-white text-indigo-700 hover:bg-gray-100 px-4 py-2 rounded-lg font-semibold transition-colors">انضم الآن</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @yield('content')
</body>
</html>
