<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="description" content="{{ config('seo.default_description', 'منصة تعليمية للرياضيات - مستر طارق الداجن. كورسات، امتحانات، ومراجعات لجميع المراحل.') }}">
        <meta name="robots" content="index, follow">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ config('seo.site_name', 'منصة الطارق في الرياضيات') }} - مستر طارق الداجن</title>
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ config('seo.site_name') }} - مستر طارق الداجن">
    <meta property="og:description" content="{{ config('seo.default_description') }}">
    <meta property="og:image" content="{{ config('seo.default_image') ?? asset('favicon.svg') }}">
    <meta property="og:locale" content="ar_SA">
    <meta property="og:site_name" content="{{ config('seo.site_name') }}">

    <!-- الخطوط العربية الاحترافية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
            <style>
        * {
            font-family: 'Cairo', system-ui, sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(60px) translateY(60px); }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: background 0.35s ease, backdrop-filter 0.35s ease;
            position: relative;
            overflow: hidden;
        }
        /* الناف بار عند التمرير: لون أسود شفاف لوضوح أفضل على الأقسام البيضاء */
        #main-nav.nav-scrolled {
            background: rgba(0, 0, 0, 0.88) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom-color: rgba(255, 255, 255, 0.15);
        }

        .glass-effect::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
            opacity: 0;
        }

        .glass-effect:hover::before {
            animation: shimmer 1.5s ease-in-out;
            opacity: 1;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.6s ease;
        }

        .card-hover:hover::before {
            left: 100%;
        }

        .card-hover:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            animation: float 8s ease-in-out infinite;
            transition: all 0.3s ease;
        }

        .floating-shape:nth-child(1) { animation-delay: 0s; }
        .floating-shape:nth-child(2) { animation-delay: 2s; }
        .floating-shape:nth-child(3) { animation-delay: 4s; }
        .floating-shape:nth-child(4) { animation-delay: 6s; }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg) scale(1); 
                opacity: 0.1;
            }
            25% { 
                transform: translateY(-30px) rotate(90deg) scale(1.1); 
                opacity: 0.2;
            }
            50% { 
                transform: translateY(-20px) rotate(180deg) scale(0.9); 
                opacity: 0.15;
            }
            75% { 
                transform: translateY(-40px) rotate(270deg) scale(1.05); 
                opacity: 0.25;
            }
        }

        .floating-numbers {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-number {
            position: absolute;
            color: rgba(102, 126, 234, 0.1);
            font-size: 2rem;
            font-weight: bold;
            animation: floatNumber 15s linear infinite;
        }

        @keyframes floatNumber {
            0% {
                transform: translateY(100vh) rotate(0deg) scale(0.5);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translateY(90vh) rotate(36deg) scale(0.7);
            }
            50% {
                opacity: 0.8;
                transform: translateY(50vh) rotate(180deg) scale(1);
            }
            90% {
                opacity: 0.3;
                transform: translateY(10vh) rotate(324deg) scale(0.8);
            }
            100% {
                transform: translateY(-10vh) rotate(360deg) scale(0.3);
                opacity: 0;
            }
        }

        .number-counter {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
            cursor: default;
        }

        .number-counter:hover {
            transform: scale(1.1);
            filter: drop-shadow(0 5px 15px rgba(102, 126, 234, 0.3));
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.6s ease;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:active {
            transform: translateY(-1px) scale(1.02);
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid #667eea;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-outline::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.4s ease;
            z-index: -1;
        }

        .btn-outline:hover::before {
            left: 0;
        }

        .btn-outline:hover {
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        }

        .feature-icon-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .feature-icon-hover::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            border-radius: inherit;
            transform: translate(-50%, -50%) scale(0);
            transition: all 0.4s ease;
        }

        .feature-icon-hover:hover::before {
            transform: translate(-50%, -50%) scale(1.2);
        }

        .feature-icon-hover:hover {
            transform: rotateY(180deg) scale(1.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .course-card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .course-card-hover:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        .course-card-hover .course-header {
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .course-card-hover:hover .course-header {
            transform: scale(1.05);
        }

        .course-card-hover .course-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
            opacity: 0;
        }

        .course-card-hover:hover .course-header::before {
            animation: courseShimmer 1s ease-in-out;
            opacity: 1;
        }

        @keyframes courseShimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .star-rating {
            transition: all 0.3s ease;
        }

        .star-rating:hover {
            transform: scale(1.1);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .bounce-animation {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .rotate-animation {
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Background particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: particleFloat 10s infinite linear;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Interactive text effects */
        .text-glow:hover {
            text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
            transition: all 0.3s ease;
        }

        /* Logo animation */
        .logo-animation {
            transition: all 0.4s ease;
        }

        .logo-animation:hover {
            transform: scale(1.1) rotate(5deg);
        }

        /* Smooth scroll behavior + منع مشاكل الجوال والعودة للصفحة */
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        body {
            overflow-x: hidden;
            min-height: 100vh;
            min-height: 100dvh;
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 768px) {
            .floating-number { animation-duration: 25s; opacity: 0.06; }
            .glass-effect::before { display: none; }
        }

        /* إضافة padding للأقسام للتعامل مع navbar الثابت */
        section[id] {
            scroll-margin-top: 100px;
        }

        /* Loading animations */
        .fade-in {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            0% { opacity: 0; transform: translateX(-50px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes slideInRight {
            0% { opacity: 0; transform: translateX(50px); }
            100% { opacity: 1; transform: translateX(0); }
        }
            </style>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['Cairo', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    </head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false }">

    <!-- Navigation Header -->
    <nav id="main-nav" class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-white/20 shadow-2xl transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <!-- Enhanced Logo -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 rounded-2xl flex items-center justify-center logo-animation shadow-xl">
                            <i class="fas fa-calculator text-white text-2xl rotate-animation"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full pulse-animation"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-white text-glow">منصة الطارق</h1>
                        <p class="text-sm text-white/80 font-medium">أكاديمية الرياضيات المتخصصة</p>
                        <p class="text-xs text-white/60">مع مستر طارق الداجن</p>
                    </div>
                </div>

                <!-- Enhanced Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-10 space-x-reverse">
                    <a href="#home" class="relative text-white font-bold text-lg nav-link group">
                        <span class="relative z-10">الرئيسية</span>
                        <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="#about" class="relative text-white/80 hover:text-white font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">عن المعلم</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="{{ route('catalog.index') }}" class="relative text-white/80 hover:text-white font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">كورسات</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="#features" class="relative text-white/80 hover:text-white font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">المميزات</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                </div>

                <!-- Enhanced Auth Buttons -->
                <div class="hidden lg:flex items-center space-x-4 space-x-reverse">
            @if (Route::has('login'))
                    @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600">
                                <i class="fas fa-tachometer-alt bounce-animation"></i>
                                <span>لوحة التحكم</span>
                        </a>
                    @else
                            <a href="{{ route('login') }}" class="text-white/80 hover:text-white font-medium px-6 py-3 rounded-full transition-all duration-300 hover:bg-white/10">
                                <i class="fas fa-sign-in-alt ml-2"></i>
                                دخول
                            </a>
                            <a href="{{ route('register') }}" class="btn-primary bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 shadow-lg">
                                <i class="fas fa-user-plus pulse-animation"></i>
                                <span>انضم الآن</span>
                            </a>
                        @endauth
                        @endif
                </div>

                <!-- Enhanced Mobile Menu Button -->
                <div class="lg:hidden">
                    <button @click="mobileMenu = !mobileMenu" 
                            class="relative w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all duration-300">
                        <i class="fas fa-bars text-xl" x-show="!mobileMenu"></i>
                        <i class="fas fa-times text-xl" x-show="mobileMenu"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Mobile Menu -->
        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="lg:hidden bg-white/95 backdrop-blur-xl border-t border-white/20">
            <div class="px-6 py-8 space-y-6">
                <a href="#home" class="block text-gray-900 font-bold text-xl py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-home ml-3 text-blue-500"></i>
                    الرئيسية
                </a>
                <a href="#about" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-user-tie ml-3 text-green-500"></i>
                    عن المعلم
                </a>
                <a href="{{ route('catalog.index') }}" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-graduation-cap ml-3 text-purple-500"></i>
                    كورسات
                </a>
                <a href="#features" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-star ml-3 text-orange-500"></i>
                    المميزات
                </a>
                
                @if (Route::has('login'))
                    <div class="pt-6 space-y-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary w-full justify-center bg-gradient-to-r from-green-500 to-teal-500">
                                <i class="fas fa-tachometer-alt"></i>
                                لوحة التحكم
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-outline w-full justify-center border-2 border-blue-600 text-blue-600">
                                <i class="fas fa-sign-in-alt"></i>
                                تسجيل الدخول
                            </a>
                            <a href="{{ route('register') }}" class="btn-primary w-full justify-center bg-gradient-to-r from-orange-500 to-red-500">
                                <i class="fas fa-user-plus"></i>
                                إنشاء حساب
                            </a>
                    @endauth
                    </div>
            @endif
            </div>
        </div>
    </nav>

    <!-- Floating Numbers Background -->
    <div class="floating-numbers">
        <div class="floating-number" style="left: 10%; animation-delay: 0s;">π</div>
        <div class="floating-number" style="left: 20%; animation-delay: 3s;">∞</div>
        <div class="floating-number" style="left: 30%; animation-delay: 6s;">√</div>
        <div class="floating-number" style="left: 40%; animation-delay: 9s;">∑</div>
        <div class="floating-number" style="left: 50%; animation-delay: 12s;">∫</div>
        <div class="floating-number" style="left: 60%; animation-delay: 15s;">θ</div>
        <div class="floating-number" style="left: 70%; animation-delay: 18s;">Δ</div>
        <div class="floating-number" style="left: 80%; animation-delay: 21s;">α</div>
        <div class="floating-number" style="left: 90%; animation-delay: 24s;">β</div>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient min-h-screen flex items-center relative overflow-hidden pt-28 sm:pt-32">
        <!-- Background Particles -->
        <div class="particles">
            <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
            <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
            <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
            <div class="particle" style="left: 40%; animation-delay: 6s;"></div>
            <div class="particle" style="left: 50%; animation-delay: 8s;"></div>
            <div class="particle" style="left: 60%; animation-delay: 10s;"></div>
            <div class="particle" style="left: 70%; animation-delay: 12s;"></div>
            <div class="particle" style="left: 80%; animation-delay: 14s;"></div>
            <div class="particle" style="left: 90%; animation-delay: 16s;"></div>
        </div>

        <!-- Floating Shapes -->
        <div class="floating-shape top-20 right-20 w-20 h-20">
            <svg viewBox="0 0 100 100" class="w-full h-full text-blue-400">
                <polygon points="50,10 90,80 10,80" fill="currentColor"/>
                                    </svg>
        </div>
        <div class="floating-shape top-40 left-10 w-16 h-16" style="animation-delay: 2s">
            <svg viewBox="0 0 100 100" class="w-full h-full text-purple-400">
                <circle cx="50" cy="50" r="40" fill="currentColor"/>
            </svg>
        </div>
        <div class="floating-shape bottom-20 right-40 w-12 h-12" style="animation-delay: 4s">
            <svg viewBox="0 0 100 100" class="w-full h-full text-cyan-400">
                <rect x="25" y="25" width="50" height="50" fill="currentColor" transform="rotate(45 50 50)"/>
            </svg>
        </div>
        <div class="floating-shape top-60 left-1/3 w-14 h-14" style="animation-delay: 6s">
            <svg viewBox="0 0 100 100" class="w-full h-full text-green-400">
                <path d="M50,10 L90,30 L90,70 L50,90 L10,70 L10,30 Z" fill="currentColor"/>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div class="text-white slide-in-left">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight">
                        منصة <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400 text-glow">الطارق</span>
                        <br>
                        <span class="text-3xl md:text-4xl lg:text-5xl text-yellow-400 pulse-animation">في الرياضيات</span>
                    </h1>
                    
                    <div class="mb-6">
                        <p class="text-2xl md:text-3xl font-bold text-yellow-300 mb-4 bounce-animation">
                            🎓 مع مستر طارق الداجن
                        </p>
                        <div class="w-20 h-1 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-full pulse-animation"></div>
                    </div>

                    <p class="text-xl md:text-2xl text-gray-300 mb-10 leading-relaxed fade-in">
                        اكتشف عالم الرياضيات السحري حيث تتحول الأرقام إلى مغامرات مذهلة والمعادلات إلى قصص ملهمة
                    </p>

                    <div class="flex flex-col sm:flex-row gap-6 fade-in">
                        <a href="{{ route('register') }}" class="btn-primary text-lg">
                            <i class="fas fa-rocket bounce-animation"></i>
                            ابدأ رحلتك الآن
                        </a>
                        <a href="#courses" class="btn-outline text-lg text-white border-white hover:bg-white hover:text-gray-900">
                            <i class="fas fa-play-circle pulse-animation"></i>
                            شاهد الكورسات
                        </a>
                    </div>
                </div>

                <!-- Dashboard Preview -->
                <div class="relative">
                    <div class="glass-effect rounded-3xl p-8 backdrop-blur-xl">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-white">لوحة التحكم</h3>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <div class="text-3xl font-black text-cyan-400">1,250</div>
                                    <div class="text-sm text-white/80">طالب نشط</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <div class="text-3xl font-black text-green-400">98%</div>
                                    <div class="text-sm text-white/80">نسبة النجاح</div>
                                </div>
                            </div>
                            
                            <div class="bg-white/10 rounded-xl p-4 flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-square-root-alt text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-white">الجبر المتقدم</h4>
                                    <div class="w-full bg-white/20 rounded-full h-2 mt-1">
                                        <div class="bg-blue-400 h-2 rounded-full w-4/5"></div>
                                    </div>
                                </div>
                                <div class="text-yellow-400 font-bold">85%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inspirational Quotes Section -->
    <section class="py-20 bg-gradient-to-r from-blue-50 to-purple-50 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="math-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="currentColor"/>
                        <circle cx="18" cy="18" r="1" fill="currentColor"/>
                        <circle cx="10" cy="10" r="0.5" fill="currentColor"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#math-pattern)"/>
                                    </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 text-glow">
                    حكم وأقوال <span class="text-blue-600 pulse-animation">ملهمة</span>
                </h2>
                <p class="text-xl text-gray-600">رحلة التعلم تبدأ بخطوة واحدة والرياضيات هي لغة الكون</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8" x-data="{ activeQuote: 0 }" x-init="setInterval(() => activeQuote = (activeQuote + 1) % 4, 5000)">
                <!-- Quote 1 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl card-hover fade-in relative overflow-hidden"
                     :class="activeQuote === 0 ? 'ring-4 ring-blue-500 scale-105' : ''">
                    <div class="absolute top-4 right-6 text-6xl text-blue-100 font-serif">"</div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-6 rotate-animation">
                            <i class="fas fa-infinity text-white text-2xl"></i>
                        </div>
                        <blockquote class="text-xl font-bold text-gray-800 mb-4 text-center leading-relaxed">
                            "الرياضيات ليست مجرد أرقام، بل هي لغة تفسر أسرار الكون"
                        </blockquote>
                        <cite class="text-blue-600 font-medium block text-center">- مستر طارق الداجن</cite>
                    </div>
                </div>

                <!-- Quote 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl card-hover fade-in relative overflow-hidden" style="animation-delay: 0.2s;"
                     :class="activeQuote === 1 ? 'ring-4 ring-green-500 scale-105' : ''">
                    <div class="absolute top-4 right-6 text-6xl text-green-100 font-serif">"</div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-6 bounce-animation">
                            <i class="fas fa-lightbulb text-white text-2xl"></i>
                        </div>
                        <blockquote class="text-xl font-bold text-gray-800 mb-4 text-center leading-relaxed">
                            "في كل مسألة رياضية معقدة، يكمن حل بسيط ينتظر من يكتشفه"
                        </blockquote>
                        <cite class="text-green-600 font-medium block text-center">- أرخميدس</cite>
                    </div>
                </div>

                <!-- Quote 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl card-hover fade-in relative overflow-hidden" style="animation-delay: 0.4s;"
                     :class="activeQuote === 2 ? 'ring-4 ring-purple-500 scale-105' : ''">
                    <div class="absolute top-4 right-6 text-6xl text-purple-100 font-serif">"</div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 pulse-animation">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <blockquote class="text-xl font-bold text-gray-800 mb-4 text-center leading-relaxed">
                            "التعلم رحلة لا تنتهي، وكل خطأ هو درس يقربنا من النجاح"
                        </blockquote>
                        <cite class="text-purple-600 font-medium block text-center">- ألبرت أينشتاين</cite>
                    </div>
                </div>

                <!-- Quote 4 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl card-hover fade-in relative overflow-hidden" style="animation-delay: 0.6s;"
                     :class="activeQuote === 3 ? 'ring-4 ring-orange-500 scale-105' : ''">
                    <div class="absolute top-4 right-6 text-6xl text-orange-100 font-serif">"</div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6 rotate-animation">
                            <i class="fas fa-rocket text-white text-2xl"></i>
                        </div>
                        <blockquote class="text-xl font-bold text-gray-800 mb-4 text-center leading-relaxed">
                            "الفهم الحقيقي يأتي من الممارسة والصبر والشغف بالتعلم"
                        </blockquote>
                        <cite class="text-orange-600 font-medium block text-center">- مستر طارق الداجن</cite>
                    </div>
                </div>
            </div>

            <!-- Interactive Progress Indicator -->
            <div class="flex justify-center mt-12 space-x-3 space-x-reverse">
                <template x-for="i in 4" :key="i">
                    <button @click="activeQuote = i - 1" 
                            class="w-4 h-4 rounded-full transition-all duration-300 hover:scale-125"
                            :class="activeQuote === i - 1 ? 'bg-blue-600 scale-125' : 'bg-gray-300'">
                    </button>
                </template>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="slide-in-left">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 text-glow">
                        رحلتك مع <span class="text-blue-600 pulse-animation">مستر طارق</span>
                    </h2>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed fade-in">
                        معلم متخصص في الرياضيات بخبرة تزيد عن 15 سنة، يقدم تجربة تعليمية شخصية ومميزة تجمع بين الأساليب التدريسية الكلاسيكية والتقنيات الحديثة لضمان فهم عميق وتفوق حقيقي.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4 card-hover p-4 rounded-xl">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 feature-icon-hover">
                                <i class="fas fa-user-graduate text-blue-600 text-xl pulse-animation"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 text-glow">تعليم شخصي ومتابعة فردية</h3>
                                <p class="text-gray-600">كل طالب له أسلوب تعلم مختلف، ولذلك أقدم متابعة شخصية لضمان فهم كل طالب للمادة بطريقته الخاصة</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 card-hover p-4 rounded-xl">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 feature-icon-hover">
                                <i class="fas fa-brain text-green-600 text-xl bounce-animation"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 text-glow">فهم عميق وليس حفظ</h3>
                                <p class="text-gray-600">أركز على فهم المفاهيم الأساسية والتفكير المنطقي، لا على الحفظ الأعمى، لتصبح الرياضيات سهلة وممتعة</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 card-hover p-4 rounded-xl">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 feature-icon-hover">
                                <i class="fas fa-heart text-purple-600 text-xl rotate-animation"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 text-glow">بيئة تعليمية محفزة</h3>
                                <p class="text-gray-600">أؤمن أن التعلم يجب أن يكون ممتعاً ومحفزاً، لذا أخلق بيئة إيجابية تشجع على طرح الأسئلة والاستكشاف</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 card-hover p-4 rounded-xl">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0 feature-icon-hover">
                                <i class="fas fa-trophy text-orange-600 text-xl pulse-animation"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 text-glow">نتائج مثبتة ونجاح مستمر</h3>
                                <p class="text-gray-600">سجل حافل من النجاحات مع طلابي، حيث تحسنت درجاتهم بشكل ملحوظ وأصبحوا يحبون الرياضيات</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative slide-in-right">
                    <div class="bg-gradient-to-br from-white to-blue-50 rounded-3xl p-8 shadow-2xl card-hover border border-blue-100">
                        <!-- الصورة والمعلومات الأساسية -->
                        <div class="text-center mb-8">
                            <div class="relative mx-auto mb-6">
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 rounded-3xl flex items-center justify-center mx-auto shadow-xl feature-icon-hover">
                                    <i class="fas fa-user-tie text-white text-3xl"></i>
                                </div>
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-star text-white text-sm pulse-animation"></i>
                                </div>
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 mb-2 text-glow">مستر طارق الداجن</h3>
                            <p class="text-blue-600 font-bold text-lg mb-2">معلم الرياضيات المتخصص</p>
                            <p class="text-gray-500 text-sm">أكاديمية الطارق للرياضيات</p>
                        </div>
                        
                        <!-- الإحصائيات والإنجازات -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-4 text-center card-hover">
                                    <div class="text-3xl font-black text-blue-600 number-counter pulse-animation">15+</div>
                                    <div class="text-sm text-gray-700 font-medium">سنة خبرة</div>
                                </div>
                                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-2xl p-4 text-center card-hover">
                                    <div class="text-3xl font-black text-green-600 number-counter bounce-animation">1500+</div>
                                    <div class="text-sm text-gray-700 font-medium">طالب متفوق</div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-2xl p-4 text-center card-hover">
                                    <div class="text-3xl font-black text-purple-600 number-counter rotate-animation">95%</div>
                                    <div class="text-sm text-gray-700 font-medium">نسبة التحسن</div>
                                </div>
                                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-4 text-center card-hover">
                                    <div class="text-3xl font-black text-orange-600 number-counter pulse-animation">25+</div>
                                    <div class="text-sm text-gray-700 font-medium">كورس متخصص</div>
                                </div>
                            </div>
                            
                            <!-- شهادات ومؤهلات -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-4">
                                <h4 class="font-bold text-gray-800 mb-3 text-center">🏆 الإنجازات والمؤهلات</h4>
                                <div class="space-y-2 text-sm text-gray-600 text-center">
                                    <p><i class="fas fa-medal text-gold-500 ml-2"></i>بكالوريوس الرياضيات - جامعة معتمدة</p>
                                    <p><i class="fas fa-certificate text-blue-500 ml-2"></i>شهادة في أساليب التدريس الحديثة</p>
                                    <p><i class="fas fa-trophy text-green-500 ml-2"></i>جائزة أفضل معلم رياضيات للعام</p>
                                </div>
                            </div>
                            
                            <!-- رسالة شخصية -->
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white text-center relative overflow-hidden">
                                <div class="absolute top-2 right-4 text-4xl opacity-20">"</div>
                                <p class="text-sm leading-relaxed font-medium relative z-10">
                                    "رسالتي هي تحويل الرياضيات من مادة معقدة إلى رحلة ممتعة من الاكتشاف والفهم. أؤمن أن كل طالب قادر على التفوق بالطريقة الصحيحة والدعم المناسب."
                                </p>
                                <p class="text-xs mt-3 opacity-80">- مستر طارق الداجن</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6">
                    مميزات <span class="text-blue-600">منصتنا</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    نوفر مجموعة شاملة من الأدوات والميزات المتطورة لضمان تجربة تعليمية استثنائية
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center card-hover bg-white p-8 rounded-2xl shadow-lg fade-in">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 feature-icon-hover">
                        <i class="fas fa-play-circle text-white text-2xl pulse-animation"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-glow">فيديوهات تفاعلية</h3>
                    <p class="text-gray-600">شروحات مرئية عالية الجودة مع إمكانية التفاعل والتحكم في سرعة التشغيل</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center card-hover bg-white p-8 rounded-2xl shadow-lg fade-in" style="animation-delay: 0.2s;">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 feature-icon-hover">
                        <i class="fas fa-clipboard-list text-white text-2xl bounce-animation"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-glow">اختبارات ذكية</h3>
                    <p class="text-gray-600">نظام تقييم متطور مع تصحيح فوري وتحليل دقيق لنقاط القوة والضعف</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center card-hover bg-white p-8 rounded-2xl shadow-lg fade-in" style="animation-delay: 0.4s;">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 feature-icon-hover">
                        <i class="fas fa-chart-line text-white text-2xl rotate-animation"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-glow">تتبع التقدم</h3>
                    <p class="text-gray-600">مراقبة مستمرة لأداء الطالب مع تقارير مفصلة وتوصيات للتحسين</p>
                </div>

                <!-- Feature 4 -->
                <div class="text-center card-hover bg-white p-8 rounded-2xl shadow-lg fade-in" style="animation-delay: 0.6s;">
                    <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-6 feature-icon-hover">
                        <i class="fas fa-mobile-alt text-white text-2xl pulse-animation"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-glow">متوافق مع الجوال</h3>
                    <p class="text-gray-600">تصميم متجاوب يعمل بسلاسة على جميع الأجهزة والشاشات</p>
                </div>

                <!-- Feature 5 -->
                <div class="text-center card-hover bg-white p-8 rounded-2xl shadow-lg fade-in" style="animation-delay: 0.8s;">
                    <div class="w-20 h-20 bg-gradient-to-r from-red-400 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 feature-icon-hover">
                        <i class="fas fa-comments text-white text-2xl bounce-animation"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-glow">دعم مباشر</h3>
                    <p class="text-gray-600">تواصل فوري مع المعلم والحصول على إجابات سريعة لجميع الاستفسارات</p>
                </div>

                <!-- Feature 6 -->
                <div class="text-center card-hover bg-white p-8 rounded-2xl shadow-lg fade-in" style="animation-delay: 1s;">
                    <div class="w-20 h-20 bg-gradient-to-r from-indigo-400 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6 feature-icon-hover">
                        <i class="fas fa-certificate text-white text-2xl rotate-animation"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-glow">شهادات معتمدة</h3>
                    <p class="text-gray-600">احصل على شهادات إتمام معتمدة عند إنهاء الكورسات بنجاح</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Academic Years Section -->
    <section id="courses" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 text-glow">
                    السنوات <span class="text-blue-600 pulse-animation">الدراسية</span>
                </h2>
                <p class="text-xl text-gray-600 fade-in">اختر صفك الدراسي وابدأ رحلتك التعليمية في الرياضيات</p>
                <div class="mt-6 inline-flex items-center px-6 py-3 bg-blue-50 rounded-full">
                    <i class="fas fa-graduation-cap text-blue-600 ml-3"></i>
                    <span class="text-blue-800 font-medium">منهج شامل ومتدرج لجميع المراحل الدراسية</span>
                </div>
            </div>

            @if($academicYears->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($academicYears as $index => $year)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden course-card-hover">
                        <div class="h-48 bg-gradient-to-br 
                            @if($index % 6 == 0) from-blue-400 to-blue-600
                            @elseif($index % 6 == 1) from-green-400 to-green-600
                            @elseif($index % 6 == 2) from-purple-400 to-purple-600
                            @elseif($index % 6 == 3) from-orange-400 to-orange-600
                            @elseif($index % 6 == 4) from-pink-400 to-pink-600
                            @else from-indigo-400 to-indigo-600
                            @endif
                            flex items-center justify-center relative overflow-hidden course-header">
                            <i class="fas fa-
                                @if($index % 6 == 0) calculator
                                @elseif($index % 6 == 1) square-root-alt
                                @elseif($index % 6 == 2) infinity
                                @elseif($index % 6 == 3) chart-line
                                @elseif($index % 6 == 4) functions
                                @else pi
                                @endif
                                text-white text-6xl pulse-animation"></i>
                            @if($year->academic_subjects_count > 0)
                                <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 bounce-animation">
                                    <span class="text-white text-sm font-medium">{{ $year->academic_subjects_count }} مادة</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $year->name }}</h3>
                            <p class="text-gray-600 mb-4">
                                @if($year->description)
                                    {{ Str::limit($year->description, 80) }}
                                @else
                                    استكشف المواد والكورسات المتاحة لهذه السنة الدراسية
                                @endif
                            </p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-book text-gray-400"></i>
                                    <span class="text-sm text-gray-600">{{ $year->academic_subjects_count }} مادة</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-layer-group text-gray-400"></i>
                                    <span class="text-sm text-gray-600">متعدد المستويات</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($year->is_active) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                        @if($year->is_active) متاح الآن @else قريباً @endif
                                    </span>
                                </div>
                                @auth
                                    <a href="{{ route('academic-years') }}" class="btn-primary text-sm px-4 py-2">
                                        استكشف المواد
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="btn-primary text-sm px-4 py-2">
                                        سجل للاستكشاف
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-graduation-cap text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">قريباً...</h3>
                        <p class="text-gray-600 mb-6">نعمل على إضافة المزيد من السنوات الدراسية لخدمتكم بشكل أفضل</p>
                        <a href="{{ route('register') }}" class="btn-primary">
                            <i class="fas fa-bell"></i>
                            اشترك للحصول على التحديثات
                        </a>
                    </div>
                </div>
            @endif

            <div class="text-center mt-12">
                <a href="{{ route('register') }}" class="btn-primary text-lg">
                    <i class="fas fa-user-plus"></i>
                    انضم للمنصة الآن
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 hero-gradient">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">
                هل أنت مستعد لبدء رحلتك؟
            </h2>
            <p class="text-xl text-gray-300 mb-10">
                انضم إلى آلاف الطلاب الذين حققوا التميز في الرياضيات مع منصة الطارق
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="{{ route('register') }}" class="btn-primary text-lg">
                    <i class="fas fa-user-plus"></i>
                    ابدأ مجاناً الآن
                </a>
                <a href="{{ route('login') }}" class="btn-outline text-lg text-white border-white hover:bg-white hover:text-gray-900">
                    <i class="fas fa-sign-in-alt"></i>
                    لدي حساب بالفعل
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo & Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calculator text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">منصة الطارق</h3>
                            <p class="text-gray-400 text-sm">في الرياضيات</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        منصة تعليمية متخصصة في الرياضيات تهدف إلى تبسيط المفاهيم الرياضية وجعلها أكثر متعة وفهماً للطلاب في جميع المراحل التعليمية.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6">روابط سريعة</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">حولنا</a></li>
                        <li><a href="#courses" class="text-gray-400 hover:text-white transition-colors">الكورسات</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">المميزات</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-lg font-bold mb-6">الدعم</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">مركز المساعدة</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">تواصل معنا</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">الأسئلة الشائعة</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400 text-sm">
                    &copy; 2024 منصة الطارق في الرياضيات. جميع الحقوق محفوظة.
                </p>
                </div>
        </div>
    </footer>

    <!-- Dynamic JavaScript -->
    <script>
        // إضافة أرقام طائرة ديناميكية
        function createFloatingNumber() {
            const numbers = ['π', '∞', '√', '∑', '∫', 'θ', 'Δ', 'α', 'β', 'γ', 'δ', 'ε', 'λ', 'μ', 'σ', 'φ', 'ψ', 'ω', '∴', '∵', '≈', '≡', '≠', '≤', '≥', '±', '∓', '×', '÷'];
            const container = document.querySelector('.floating-numbers');
            
            if (!container) return;
            
            const number = document.createElement('div');
            number.className = 'floating-number';
            number.textContent = numbers[Math.floor(Math.random() * numbers.length)];
            number.style.left = Math.random() * 100 + '%';
            number.style.animationDelay = Math.random() * 5 + 's';
            number.style.fontSize = (Math.random() * 1.5 + 1.5) + 'rem';
            number.style.color = `rgba(${Math.floor(Math.random() * 100) + 100}, ${Math.floor(Math.random() * 100) + 126}, ${Math.floor(Math.random() * 100) + 234}, 0.1)`;
            
            container.appendChild(number);
            
            // إزالة العنصر بعد انتهاء الأنيميشن
            setTimeout(() => {
                if (number.parentNode) {
                    number.parentNode.removeChild(number);
                }
            }, 15000);
        }

        // إضافة جسيمات ديناميكية
        function createParticle() {
            const particlesContainer = document.querySelector('.particles');
            if (!particlesContainer) return;
            
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 2 + 's';
            particle.style.animationDuration = (Math.random() * 5 + 8) + 's';
            
            const colors = ['rgba(255, 255, 255, 0.5)', 'rgba(102, 126, 234, 0.3)', 'rgba(118, 75, 162, 0.3)', 'rgba(240, 147, 251, 0.3)'];
            particle.style.background = colors[Math.floor(Math.random() * colors.length)];
            
            particlesContainer.appendChild(particle);
            
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 10000);
        }

        // تشغيل الوظائف على فترات
        setInterval(createFloatingNumber, 1500);
        setInterval(createParticle, 800);
        
        // تأثيرات hover متقدمة للبطاقات
        document.addEventListener('DOMContentLoaded', function() {
            // تأثيرات البطاقات ثلاثية الأبعاد
            const cards = document.querySelectorAll('.card-hover, .course-card-hover');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-15px) scale(1.02) rotateX(5deg)';
                    this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1) rotateX(0deg)';
                    this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
                });

                // تأثير الماوس ثلاثي الأبعاد
                card.addEventListener('mousemove', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = (y - centerY) / 10;
                    const rotateY = (centerX - x) / 10;
                    
                    this.style.transform = `translateY(-15px) scale(1.02) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                });
            });

            // تأثيرات العداد المتحرك
            const counters = document.querySelectorAll('.number-counter');
            const animateCounters = () => {
                counters.forEach(counter => {
                    const target = parseInt(counter.textContent);
                    const increment = target / 100;
                    let current = 0;
                    
                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            counter.textContent = Math.floor(current) + (counter.textContent.includes('%') ? '%' : '');
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target + (counter.textContent.includes('%') ? '%' : '');
                        }
                    };
                    
                    // تشغيل العداد عند ظهور العنصر في الشاشة
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                updateCounter();
                                observer.unobserve(entry.target);
                            }
                        });
                    });
                    
                    observer.observe(counter);
                });
            };

            // تشغيل الأنيميشن عند التحميل
            setTimeout(animateCounters, 500);

            // تأثيرات النجوم المتحركة
            const stars = document.querySelectorAll('.star-rating i');
            stars.forEach((star, index) => {
                star.addEventListener('mouseenter', function() {
                    for (let i = 0; i <= index; i++) {
                        stars[i].style.transform = 'scale(1.2)';
                        stars[i].style.color = '#fbbf24';
                    }
                });
                
                star.addEventListener('mouseleave', function() {
                    stars.forEach(s => {
                        s.style.transform = 'scale(1)';
                        s.style.color = '#facc15';
                    });
                });
            });

            // تأثير التمرير السلس للروابط
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // تأثير ظهور العناصر عند التمرير
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const fadeInObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // مراقبة العناصر للأنيميشن
            document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                fadeInObserver.observe(el);
            });

            // تأثير المؤشر المخصص
            document.addEventListener('mousemove', function(e) {
                // إضافة تأثير بريق يتبع المؤشر (اختياري)
                const glitter = document.createElement('div');
                glitter.style.position = 'fixed';
                glitter.style.left = e.clientX + 'px';
                glitter.style.top = e.clientY + 'px';
                glitter.style.width = '4px';
                glitter.style.height = '4px';
                glitter.style.background = 'rgba(102, 126, 234, 0.6)';
                glitter.style.borderRadius = '50%';
                glitter.style.pointerEvents = 'none';
                glitter.style.zIndex = '1000';
                glitter.style.animation = 'fadeOut 1s ease-out forwards';
                
                document.body.appendChild(glitter);
                
                setTimeout(() => {
                    if (glitter.parentNode) {
                        glitter.parentNode.removeChild(glitter);
                    }
                }, 1000);
            });
        });

        // إضافة keyframes للتأثيرات الجديدة
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                0% { opacity: 1; transform: scale(1); }
                100% { opacity: 0; transform: scale(0); }
            }
            
            .star-rating i {
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .parallax-bg {
                transform: translateZ(0);
                will-change: transform;
            }
            
            /* تأثيرات إضافية للأنيميشن */
            .enhanced-glow:hover {
                text-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
                transform: scale(1.05);
                transition: all 0.3s ease;
            }
            
            .floating-icon {
                animation: floatingIcon 4s ease-in-out infinite;
            }
            
            @keyframes floatingIcon {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-15px) rotate(5deg); }
            }
            
            .gradient-border {
                position: relative;
                background: linear-gradient(45deg, #667eea, #764ba2, #f093fb);
                background-size: 200% 200%;
                animation: gradientShift 3s ease infinite;
            }
            
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            .typewriter {
                overflow: hidden;
                border-right: 2px solid #667eea;
                white-space: nowrap;
                animation: typewriter 4s steps(30) 1s 1 normal both, blinkCursor 1s steps(2) infinite;
            }
            
            @keyframes typewriter {
                from { width: 0; }
                to { width: 100%; }
            }
            
            @keyframes blinkCursor {
                from { border-right-color: #667eea; }
                to { border-right-color: transparent; }
            }
        `;
        document.head.appendChild(style);
    </script>

    <script>
    (function() {
        var nav = document.getElementById('main-nav');
        var scrollThreshold = 40;
        function updateNav() {
            if (!nav) return;
            if (window.scrollY > scrollThreshold) nav.classList.add('nav-scrolled');
            else nav.classList.remove('nav-scrolled');
        }
        window.addEventListener('scroll', function() { requestAnimationFrame(updateNav); }, { passive: true });
        window.addEventListener('pageshow', function(e) {
            updateNav();
            if (e.persisted) { window.scrollTo(0, window.scrollY); document.body.offsetHeight; }
        });
        updateNav();
    })();
    </script>

    </body>
</html>
