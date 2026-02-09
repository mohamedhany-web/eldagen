<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>@yield('title', 'منصة الطارق') - مستر طارق الداجن</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', system-ui, sans-serif; }
        .glass-effect {
            background: rgba(15, 12, 41, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: background 0.35s ease, backdrop-filter 0.35s ease;
        }
        #main-nav.nav-scrolled {
            background: rgba(0, 0, 0, 0.88) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom-color: rgba(255, 255, 255, 0.15);
        }
        .nav-link { position: relative; transition: all 0.3s ease; }
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
        .nav-link:hover::after { width: 100%; }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .btn-outline {
            background: transparent;
            color: #667eea;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            border: 2px solid #667eea;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        .logo-animation { transition: all 0.4s ease; }
        .logo-animation:hover { transform: scale(1.05) rotate(3deg); }
        .text-glow { text-shadow: 0 0 20px rgba(255,255,255,0.3); }
        .pulse-animation { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .rotate-animation { animation: rotate 4s linear infinite; }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .bounce-animation { animation: bounce 2s infinite; }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-8px); } 60% { transform: translateY(-4px); } }
        section[id] { scroll-margin-top: 100px; }
        html { scroll-behavior: smooth; overflow-x: hidden; }
        body { overflow-x: hidden; min-height: 100vh; -webkit-overflow-scrolling: touch; }
    </style>
    @stack('styles')
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 'arabic': ['Cairo', 'system-ui', 'sans-serif'] }
                }
            }
        };
    </script>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ mobileMenu: false }">

    <!-- نفس الناف بار الخاص بالصفحة الرئيسية -->
    <nav id="main-nav" class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-white/20 shadow-2xl transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="relative">
                        <a href="{{ url('/') }}" class="block">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 rounded-2xl flex items-center justify-center logo-animation shadow-xl">
                                <i class="fas fa-calculator text-white text-2xl rotate-animation"></i>
                            </div>
                        </a>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full pulse-animation"></div>
                    </div>
                    <div>
                        <a href="{{ url('/') }}" class="block">
                            <h1 class="text-2xl font-black text-white text-glow">منصة الطارق</h1>
                            <p class="text-sm text-white/80 font-medium">أكاديمية الرياضيات المتخصصة</p>
                            <p class="text-xs text-white/60">مع مستر طارق الداجن</p>
                        </a>
                    </div>
                </div>

                <div class="hidden lg:flex items-center space-x-10 space-x-reverse">
                    <a href="{{ url('/') }}" class="relative text-white/90 hover:text-white font-medium text-lg nav-link group">
                        <span class="relative z-10">الرئيسية</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="{{ url('/#about') }}" class="relative text-white/80 hover:text-white font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">عن المعلم</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="{{ route('catalog.index') }}" class="relative {{ request()->routeIs('catalog.*') ? 'text-white font-bold' : 'text-white/80 hover:text-white' }} font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">كورسات</span>
                        <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="{{ url('/#features') }}" class="relative text-white/80 hover:text-white font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">المميزات</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                    <a href="{{ route('parent-report.index') }}" class="relative {{ request()->routeIs('parent-report.*') ? 'text-white font-bold' : 'text-white/80 hover:text-white' }} font-medium text-lg nav-link group transition-all duration-300">
                        <span class="relative z-10">تقارير ولي الأمر</span>
                        <div class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                    </a>
                </div>

                <div class="hidden lg:flex items-center space-x-4 space-x-reverse">
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
                </div>

                <div class="lg:hidden">
                    <button @click="mobileMenu = !mobileMenu" class="relative w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all duration-300">
                        <i class="fas fa-bars text-xl" x-show="!mobileMenu"></i>
                        <i class="fas fa-times text-xl" x-show="mobileMenu"></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenu" x-transition class="lg:hidden bg-white/95 backdrop-blur-xl border-t border-white/20">
            <div class="px-6 py-8 space-y-6">
                <a href="{{ url('/') }}" class="block text-gray-900 font-bold text-xl py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-home ml-3 text-blue-500"></i>
                    الرئيسية
                </a>
                <a href="{{ url('/#about') }}" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-user-tie ml-3 text-green-500"></i>
                    عن المعلم
                </a>
                <a href="{{ route('catalog.index') }}" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-graduation-cap ml-3 text-purple-500"></i>
                    كورسات
                </a>
                <a href="{{ url('/#features') }}" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-star ml-3 text-orange-500"></i>
                    المميزات
                </a>
                <a href="{{ route('parent-report.index') }}" class="block text-gray-700 font-medium text-lg py-3 border-b border-gray-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-user-friends ml-3 text-indigo-500"></i>
                    تقارير ولي الأمر
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary w-full justify-center bg-gradient-to-r from-green-500 to-teal-500 mt-4">
                        <i class="fas fa-tachometer-alt"></i>
                        لوحة التحكم
                    </a>
                @else
                    <div class="pt-4 space-y-3">
                        <a href="{{ route('login') }}" class="btn-outline w-full justify-center border-2 border-blue-600 text-blue-600 block text-center">
                            <i class="fas fa-sign-in-alt"></i>
                            دخول
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary w-full justify-center bg-gradient-to-r from-orange-500 to-red-500 block text-center">
                            <i class="fas fa-user-plus"></i>
                            انضم الآن
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- المحتوى الرئيسي - خلفية بيضاء مثل الصفحة الرئيسية -->
    <main class="pt-24 min-h-screen bg-white">
        @yield('content')
    </main>

    <!-- نفس الفوتر الخاص بالصفحة الرئيسية -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
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
                <div>
                    <h4 class="text-lg font-bold mb-6">روابط سريعة</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/#about') }}" class="text-gray-400 hover:text-white transition-colors">حولنا</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="text-gray-400 hover:text-white transition-colors">الكورسات</a></li>
                        <li><a href="{{ url('/#features') }}" class="text-gray-400 hover:text-white transition-colors">المميزات</a></li>
                    </ul>
                </div>
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
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} منصة الطارق في الرياضيات. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

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
