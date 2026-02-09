<!DOCTYPE html>
<html lang="ar" dir="rtl" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('seo.default_description'))">
    <meta name="robots" content="@yield('meta_robots', 'noindex, nofollow')">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="canonical" href="{{ url()->current() }}">

    <title>@yield('title', 'لوحة التحكم') - {{ config('seo.site_name', config('app.name')) }}</title>

    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', config('seo.site_name'))">
    <meta property="og:description" content="@yield('og_description', config('seo.default_description'))">
    <meta property="og:image" content="@yield('og_image', config('seo.default_image') ?? asset('favicon.svg'))">
    <meta property="og:locale" content="{{ config('seo.locale') }}">
    <meta property="og:site_name" content="{{ config('seo.site_name') }}">

    <!-- الخطوط العربية الاحترافية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- تخصيص TailwindCSS -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- حماية المنصة من التصوير -->
    <script>
        window.Laravel = {
            user: {
                name: '{{ auth()->check() ? auth()->user()->name : "زائر" }}'
            }
        };
    </script>
    <script src="{{ asset('js/platform-protection.js') }}"></script>

    @auth
    @php $userTheme = auth()->user()->preferences['theme'] ?? null; @endphp
    @if($userTheme === 'dark' || $userTheme === 'light')
    <script>
      (function(){
        var t = @json($userTheme);
        if (t === 'dark') { localStorage.setItem('darkMode', 'true'); document.documentElement.classList.add('dark'); }
        else if (t === 'light') { localStorage.setItem('darkMode', 'false'); document.documentElement.classList.remove('dark'); }
      })();
    </script>
    @endif
    @endauth

    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif;
        }
        
        .dark-mode-toggle {
            transition: all 0.3s ease;
        }
        
        .sidebar-transition {
            transition: transform 0.3s ease;
        }

        /* تحسين المخرولة للوضع المظلم */
        .dark .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-track {
            background: #374151;
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #6b7280;
            border-radius: 3px;
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* تحسين الشريط الجانبي */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: #6b7280 #374151;
        }

        /* ألوان المنصة — بنفسجي/أزرق (#667eea → #764ba2) */
        :root {
            --platform-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --platform-gradient-r: linear-gradient(to right, #667eea, #764ba2);
            --platform-soft: linear-gradient(to right, rgba(102, 126, 234, 0.12), rgba(118, 75, 162, 0.12));
            --platform-primary: #667eea;
            --platform-primary-dark: #5a67d8;
        }
        .dark {
            --platform-soft: linear-gradient(to right, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
        }

        /* كاردات لوحة التحكم */
        .dashboard-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(102, 126, 234, 0.25);
        }
        .dark .dashboard-card:hover {
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.4);
        }

        /* السايدبار — عنصر نشط (تدرج المنصة + نص أبيض) */
        .sidebar-nav-item-active {
            background: var(--platform-gradient-r) !important;
            color: white !important;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.35);
        }
        .sidebar-nav-item-active .sidebar-icon-box {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        /* السايدبار — عنصر غير نشط مع hover */
        .sidebar-nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-nav-link:hover {
            transform: translateX(-3px);
        }
        .sidebar-nav-link:hover:not(.sidebar-nav-item-active) {
            background: var(--platform-soft) !important;
            color: #667eea !important;
        }
        .dark .sidebar-nav-link:hover:not(.sidebar-nav-item-active) {
            background: var(--platform-soft) !important;
            color: #a5b4fc !important;
        }
        .sidebar-icon-box {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .sidebar-nav-link:hover:not(.sidebar-nav-item-active) .sidebar-icon-box {
            background: rgba(102, 126, 234, 0.15) !important;
            transform: scale(1.1);
        }
        .dark .sidebar-nav-link:hover:not(.sidebar-nav-item-active) .sidebar-icon-box {
            background: rgba(102, 126, 234, 0.25) !important;
        }
        .sidebar-sub-link {
            transition: all 0.2s ease;
        }
        .sidebar-sub-link:hover {
            padding-right: 0.5rem;
        }
        .sidebar .scrollbar-custom::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar .scrollbar-custom::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }
        .sidebar .scrollbar-custom::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 10px;
        }
        .sidebar .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }

        /* السايدبار على الهاتف — مساحة آمنة للنوتش وتحسين التمرير */
        @media (max-width: 1023px) {
            .sidebar-panel-mobile {
                padding-top: env(safe-area-inset-top, 0);
                padding-bottom: env(safe-area-inset-bottom, 0);
                padding-right: env(safe-area-inset-right, 0);
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gradient-to-br dark:from-slate-900 dark:via-gray-900 dark:to-slate-950 text-gray-900 dark:text-gray-100 transition-colors duration-300" 
      x-data="{ 
          darkMode: localStorage.getItem('darkMode') === 'true' || false,
          sidebarOpen: false,
          isDesktop: false,
          checkViewport() {
              this.isDesktop = window.innerWidth >= 1024;
              if (this.isDesktop) {
                  this.sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
              } else {
                  this.sidebarOpen = false;
              }
          }
      }"
      x-init="
          checkViewport();
          window.addEventListener('resize', () => checkViewport());
          $watch('darkMode', value => {
              localStorage.setItem('darkMode', value);
              if (value) document.documentElement.classList.add('dark');
              else document.documentElement.classList.remove('dark');
          });
          $watch('sidebarOpen', value => { if (this.isDesktop) localStorage.setItem('sidebarOpen', value); });
          if (darkMode) document.documentElement.classList.add('dark');
      ">
    <div class="flex h-screen overflow-hidden">
        @auth
            <!-- الشريط الجانبي — على الديسكتوب يظهر دائماً، على الهاتف عند الطلب -->
            <div x-show="sidebarOpen || isDesktop"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="sidebar-panel-mobile fixed inset-y-0 right-0 z-50 w-[280px] max-w-[85vw] sm:w-72 lg:w-64 lg:static lg:translate-x-0 h-full flex flex-col bg-white dark:bg-gray-800 bg-gradient-to-b from-white via-emerald-50/20 to-white dark:from-gray-800 dark:via-gray-800/95 dark:to-gray-800 dark:backdrop-blur-xl shadow-2xl lg:shadow-xl border-l border-gray-200 dark:border-gray-700/80 sidebar-transition"
                 :class="{ 'lg:flex': isDesktop }">
                <!-- زر إغلاق السايدبار على الهاتف فقط -->
                <button type="button"
                        @click="sidebarOpen = false"
                        class="lg:hidden absolute top-4 left-4 z-10 p-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 transition-colors duration-200"
                        aria-label="إغلاق القائمة">
                    <i class="fas fa-times text-lg"></i>
                </button>
                @include('layouts.sidebar')
            </div>

            <!-- تراكب للهواتف — يغلق السايدبار عند النقر -->
            <div x-show="sidebarOpen && !isDesktop"
                 x-transition:enter="transition-opacity duration-300 ease-out"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-200 ease-in"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden"></div>
        @endauth

        <!-- المحتوى الرئيسي -->
        <div class="flex flex-col flex-1 overflow-hidden">
            @auth
                <!-- شريط التنقل العلوي -->
                <header class="shadow-sm border-b border-gray-200 dark:border-gray-700/80 transition-all duration-300 bg-gradient-to-r from-white via-emerald-50/30 to-teal-50/30 dark:from-slate-800/95 dark:via-gray-800/90 dark:to-slate-800/95 dark:backdrop-blur-sm">
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-4">
                            <!-- زر فتح/إغلاق الشريط الجانبي -->
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-all duration-300 lg:hidden">
                                <i class="fas fa-bars text-gray-700 dark:text-gray-300"></i>
                            </button>
                            
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">@yield('header', 'لوحة التحكم')</h1>
                        </div>

                        <div class="flex items-center gap-4">
                            @if(auth()->user()->isAdmin())
                                <!-- إرسال إشعار سريع للأدمن -->
                                <button onclick="showQuickNotificationModal()" 
                                        class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition-all duration-300"
                                        title="إرسال إشعار سريع">
                                    <i class="fas fa-paper-plane text-emerald-600 dark:text-emerald-400"></i>
                                </button>
                            @endif
                            
                            <!-- مفتاح الوضع المظلم -->
                            <button @click="darkMode = !darkMode" 
                                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors dark-mode-toggle">
                                <i x-show="!darkMode" class="fas fa-moon"></i>
                                <i x-show="darkMode" class="fas fa-sun"></i>
                            </button>

                            <!-- إشعارات -->
                            <div class="relative" x-data="{ open: false, unreadCount: 0 }" x-init="loadUnreadCount()">
                                <button @click="open = !open; if(open) loadRecentNotifications()" 
                                        class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-bell"></i>
                                    <span x-show="unreadCount > 0" 
                                          x-text="unreadCount > 99 ? '99+' : unreadCount"
                                          class="absolute -top-1 -left-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                                </button>

                                <!-- قائمة الإشعارات -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute left-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 z-50">
                                    <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
                                        <h3 class="font-semibold">الإشعارات</h3>
                                        <span x-show="unreadCount > 0" 
                                              x-text="unreadCount"
                                              class="bg-red-500 text-white text-xs px-2 py-1 rounded-full"></span>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto" id="notifications-container">
                                        <!-- سيتم تحميل الإشعارات هنا -->
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-spinner fa-spin mb-2"></i>
                                            <p class="text-sm">جاري تحميل الإشعارات...</p>
                                        </div>
                                    </div>
                                    <div class="p-4 text-center border-t dark:border-gray-700">
                                        <a href="{{ route('notifications') }}" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">عرض جميع الإشعارات</a>
                                    </div>
                                </div>
                            </div>

                            <!-- قائمة المستخدم -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center gap-2 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold bg-gradient-to-br from-blue-500 via-indigo-500 to-blue-600 shadow-md">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium hidden md:block">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>

                                <!-- قائمة منسدلة -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 z-50">
                                    <div class="p-2">
                                        <a href="{{ route('profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                                            <i class="fas fa-user"></i>
                                            الملف الشخصي
                                        </a>
                                        <a href="{{ route('settings') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                                            <i class="fas fa-cog"></i>
                                            الإعدادات
                                        </a>
                                        <hr class="my-2 border-gray-200 dark:border-gray-700">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-right text-red-600 dark:text-red-400">
                                                <i class="fas fa-sign-out-alt"></i>
                                                تسجيل الخروج
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endauth

            <!-- المحتوى -->
            <main class="flex-1 overflow-auto p-4 sm:p-6 bg-gray-50 dark:bg-transparent dark:bg-gradient-to-br dark:from-slate-900/50 dark:via-gray-900/30 dark:to-slate-950/50 min-w-0">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- نافذة الإرسال السريع للأدمن -->
    <div id="quickNotificationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">إرسال إشعار سريع</h3>
                <button onclick="hideQuickNotificationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="quickNotificationForm">
                <div class="space-y-4">
                    <!-- العنوان -->
                    <div>
                        <label for="modal_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">العنوان</label>
                        <input type="text" name="title" id="modal_title" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                               placeholder="عنوان الإشعار">
                    </div>

                    <!-- المستهدفين -->
                    <div>
                        <label for="modal_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المستهدفين</label>
                        <select name="target_type" id="modal_target" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">اختر المستهدفين</option>
                            <option value="all_students">جميع الطلاب</option>
                        </select>
                    </div>

                    <!-- النص -->
                    <div>
                        <label for="modal_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">النص</label>
                        <textarea name="message" id="modal_message" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="اكتب نص الإشعار..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 space-x-reverse mt-6">
                    <button type="button" onclick="hideQuickNotificationModal()" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors">
                        إلغاء
                    </button>
                    <button type="button" onclick="sendQuickNotification()" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane ml-2"></i>
                        إرسال
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(auth()->user()->isStudent())
    <!-- بوب أب الإشعار للطالب (ريل تايم) -->
    <div id="notification-popup-overlay" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4" onclick="closeNotificationPopup()">
        <div id="notification-popup-box" class="relative max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all" onclick="event.stopPropagation()">
            <div id="notification-popup-icon" class="absolute top-4 right-4 w-12 h-12 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-bell text-xl"></i>
            </div>
            <div class="p-6 pt-16">
                <h3 id="notification-popup-title" class="text-lg font-bold text-gray-900 dark:text-white mb-2"></h3>
                <p id="notification-popup-message" class="text-sm text-gray-600 dark:text-gray-400 mb-4"></p>
                <div class="flex items-center justify-between gap-3">
                    <a id="notification-popup-action" href="#" class="hidden text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline"></a>
                    <button type="button" onclick="markNotificationAsReadAndClose()" class="mr-auto px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        تمت القراءة
                    </button>
                </div>
            </div>
            <button type="button" onclick="closeNotificationPopup()" class="absolute top-3 left-3 p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    @stack('scripts')
    
    <!-- JavaScript للإشعارات -->
    @auth
    <script>
    function loadUnreadCount() {
        fetch('/api/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                this.unreadCount = data.count;
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
    }

    function loadRecentNotifications() {
        fetch('/api/notifications/recent')
            .then(response => response.json())
            .then(notifications => {
                const container = document.getElementById('notifications-container');
                
                if (notifications.length === 0) {
                    container.innerHTML = `
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-bell-slash mb-2"></i>
                            <p class="text-sm">لا توجد إشعارات</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                notifications.forEach(notification => {
                    const typeIcons = {
                        'general': 'fas fa-info-circle',
                        'course': 'fas fa-graduation-cap',
                        'exam': 'fas fa-clipboard-check',
                        'assignment': 'fas fa-tasks',
                        'grade': 'fas fa-star',
                        'announcement': 'fas fa-bullhorn',
                        'reminder': 'fas fa-bell',
                        'warning': 'fas fa-exclamation-triangle',
                        'system': 'fas fa-cog',
                    };

                    const typeColors = {
                        'general': 'blue',
                        'course': 'green',
                        'exam': 'purple',
                        'assignment': 'orange',
                        'grade': 'yellow',
                        'announcement': 'red',
                        'reminder': 'blue',
                        'warning': 'red',
                        'system': 'gray',
                    };

                    const icon = typeIcons[notification.type] || 'fas fa-info-circle';
                    const color = typeColors[notification.type] || 'blue';
                    
                    html += `
                        <div class="p-4 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 ${!notification.is_read ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-${color}-100 dark:bg-${color}-900 rounded-full flex items-center justify-center mt-1">
                                    <i class="${icon} text-${color}-600 dark:text-${color}-400 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">${notification.title}</p>
                                        ${!notification.is_read ? '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${notification.message.substring(0, 80)}${notification.message.length > 80 ? '...' : ''}</p>
                                    <p class="text-xs text-gray-400 mt-1">${timeAgo(notification.created_at)}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notifications-container').innerHTML = `
                    <div class="p-4 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <p class="text-sm">خطأ في تحميل الإشعارات</p>
                    </div>
                `;
            });
    }

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'الآن';
        if (diffInMinutes < 60) return `منذ ${diffInMinutes} دقيقة`;
        
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) return `منذ ${diffInHours} ساعة`;
        
        const diffInDays = Math.floor(diffInHours / 24);
        return `منذ ${diffInDays} يوم`;
    }

    // تحديث عداد الإشعارات كل دقيقة
    setInterval(loadUnreadCount, 60000);

    @if(auth()->user()->isStudent())
    // بوب أب الإشعارات للطالب — ريل تايم
    var notificationPopupShownIds = {};
    var currentPopupNotificationId = null;
    var notificationTypeIcons = { general: 'fas fa-info-circle', course: 'fas fa-graduation-cap', exam: 'fas fa-clipboard-check', assignment: 'fas fa-tasks', grade: 'fas fa-star', announcement: 'fas fa-bullhorn', reminder: 'fas fa-bell', warning: 'fas fa-exclamation-triangle', system: 'fas fa-cog' };
    var notificationTypeColors = { general: '#3b82f6', course: '#22c55e', exam: '#a855f7', assignment: '#f97316', grade: '#eab308', announcement: '#ef4444', reminder: '#3b82f6', warning: '#ef4444', system: '#6b7280' };

    function fetchUnreadForPopup() {
        fetch('{{ route("notifications.unread-for-popup") }}')
            .then(function(r) { return r.json(); })
            .then(function(list) {
                if (!Array.isArray(list) || list.length === 0) return;
                var toShow = list.filter(function(n) { return !notificationPopupShownIds[n.id]; });
                if (toShow.length > 0 && !document.getElementById('notification-popup-overlay').classList.contains('!flex')) {
                    showNotificationPopup(toShow[0]);
                }
            })
            .catch(function() {});
    }

    function showNotificationPopup(n) {
        currentPopupNotificationId = n.id;
        var overlay = document.getElementById('notification-popup-overlay');
        var iconEl = document.getElementById('notification-popup-icon');
        var titleEl = document.getElementById('notification-popup-title');
        var messageEl = document.getElementById('notification-popup-message');
        var actionEl = document.getElementById('notification-popup-action');
        var type = n.type || 'general';
        var colorHex = notificationTypeColors[type] || '#3b82f6';
        var icon = notificationTypeIcons[type] || 'fas fa-bell';
        if (iconEl) {
            iconEl.style.backgroundColor = colorHex;
            iconEl.className = 'absolute top-4 right-4 w-12 h-12 rounded-xl flex items-center justify-center text-white';
            var ico = iconEl.querySelector('i');
            if (ico) ico.className = icon + ' text-xl';
        }
        if (titleEl) titleEl.textContent = n.title || '';
        if (messageEl) messageEl.textContent = (n.message || '').substring(0, 300);
        if (actionEl) {
            if (n.action_url) {
                actionEl.href = n.action_url;
                actionEl.textContent = n.action_text || 'عرض';
                actionEl.classList.remove('hidden');
            } else {
                actionEl.classList.add('hidden');
            }
        }
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
    }

    var notificationMarkReadBase = "{{ url('/notifications') }}";
    function markNotificationAsReadAndClose() {
        if (currentPopupNotificationId) {
            fetch(notificationMarkReadBase + '/' + currentPopupNotificationId + '/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(function() {
                if (typeof loadUnreadCount === 'function') loadUnreadCount();
            }).catch(function() {});
            notificationPopupShownIds[currentPopupNotificationId] = true;
            currentPopupNotificationId = null;
        }
        closeNotificationPopup();
    }

    function closeNotificationPopup() {
        if (currentPopupNotificationId) {
            notificationPopupShownIds[currentPopupNotificationId] = true;
            fetch(notificationMarkReadBase + '/' + currentPopupNotificationId + '/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(function() { if (typeof loadUnreadCount === 'function') loadUnreadCount(); }).catch(function() {});
            currentPopupNotificationId = null;
        }
        var overlay = document.getElementById('notification-popup-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    }

    setInterval(fetchUnreadForPopup, 12000);
    setTimeout(fetchUnreadForPopup, 2000);
    @endif

    @if(auth()->user()->isAdmin())
    // نافذة الإرسال السريع للأدمن
    function showQuickNotificationModal() {
        document.getElementById('quickNotificationModal').classList.remove('hidden');
    }

    function hideQuickNotificationModal() {
        document.getElementById('quickNotificationModal').classList.add('hidden');
        document.getElementById('quickNotificationForm').reset();
    }

    function sendQuickNotification() {
        const form = document.getElementById('quickNotificationForm');
        const formData = new FormData(form);
        
        const data = {
            title: formData.get('title'),
            message: formData.get('message'),
            target_type: formData.get('target_type'),
            target_id: formData.get('target_id') || null,
        };
        
        if (!data.title || !data.message || !data.target_type) {
            alert('يرجى ملء جميع الحقول المطلوبة');
            return;
        }
        
        fetch('/admin/notifications/quick-send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                hideQuickNotificationModal();
            } else {
                alert('حدث خطأ في إرسال الإشعار');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في إرسال الإشعار');
        });
    }
    @endif
    </script>
    @endauth
</body>
</html>
