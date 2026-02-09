<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>@yield('title', 'لوحة الإدارة - مستر طارق الداجن')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
        }
        
        .sidebar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .sidebar::-webkit-scrollbar {
            display: none;
        }
        
        .nav-link {
            @apply flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-gray-700 hover:bg-gray-100 hover:text-gray-900;
        }
        
        .nav-link.active {
            @apply bg-blue-100 text-blue-700;
        }
        
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-success {
            @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-warning {
            @apply bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto bg-white border-r border-gray-200">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">مستر طارق الداجن</h2>
                            <p class="text-xs text-gray-500">لوحة الإدارة</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="mt-8 flex-1 px-4 space-y-2">
                    <!-- لوحة التحكم -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>لوحة التحكم</span>
                    </a>

                    <!-- إدارة المستخدمين -->
                    <a href="{{ route('admin.users') }}" 
                       class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <i class="fas fa-users w-5"></i>
                        <span>إدارة المستخدمين</span>
                    </a>

                    <!-- إدارة الطلبات -->
                    <a href="{{ route('admin.orders.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span>إدارة الطلبات</span>
                        @php
                            $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                        @endphp
                        @if($pendingOrders > 0)
                            <span class="mr-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingOrders }}</span>
                        @endif
                    </a>

                    <!-- أكواد التفعيل -->
                    <a href="{{ route('admin.activation-codes.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.activation-codes*') ? 'active' : '' }}">
                        <i class="fas fa-ticket-alt w-5"></i>
                        <span>أكواد التفعيل</span>
                    </a>

                    <!-- فاصل -->
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">المحتوى الأكاديمي</p>
                    </div>

                    <!-- السنوات الدراسية -->
                    <a href="{{ route('admin.academic-years.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt w-5"></i>
                        <span>السنوات الدراسية</span>
                    </a>

                    <!-- المواد الدراسية -->
                    <a href="{{ route('admin.academic-subjects.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.academic-subjects*') ? 'active' : '' }}">
                        <i class="fas fa-book w-5"></i>
                        <span>المواد الدراسية</span>
                    </a>

                    <!-- الكورسات -->
                    <a href="{{ route('admin.advanced-courses.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.advanced-courses*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap w-5"></i>
                        <span>الكورسات</span>
                    </a>

                    <!-- فاصل -->
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">النظام القديم</p>
                    </div>

                    <!-- المواد القديمة -->
                    <a href="{{ route('admin.subjects.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                        <i class="fas fa-folder w-5"></i>
                        <span>المواد القديمة</span>
                    </a>

                    <!-- الكورسات القديمة -->
                    <a href="{{ route('admin.courses.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                        <i class="fas fa-video w-5"></i>
                        <span>الكورسات القديمة</span>
                    </a>

                    <!-- فاصل -->
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">التقارير</p>
                    </div>

                    <!-- سجل النشاطات -->
                    <a href="{{ route('admin.activity-log') }}" 
                       class="nav-link {{ request()->routeIs('admin.activity-log*') ? 'active' : '' }}">
                        <i class="fas fa-history w-5"></i>
                        <span>سجل النشاطات</span>
                    </a>

                    <!-- الإحصائيات -->
                    <a href="{{ route('admin.statistics') }}" 
                       class="nav-link {{ request()->routeIs('admin.statistics*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>الإحصائيات</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" x-transition class="fixed inset-0 z-40 lg:hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <!-- Repeat sidebar content for mobile -->
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pr-64 flex flex-col flex-1">
            <!-- Top navigation -->
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow border-b border-gray-200">
                <button @click="sidebarOpen = true" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 lg:hidden">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                
                <div class="flex-1 px-4 flex justify-between items-center">
                    <div class="flex-1 flex">
                        <h1 class="text-lg font-semibold text-gray-900">
                            @yield('page_title', 'لوحة الإدارة')
                        </h1>
                    </div>
                    
                    <div class="mr-4 flex items-center md:mr-6">
                        <!-- User dropdown -->
                        <div class="mr-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="hidden md:block mr-3 text-gray-700 text-sm font-medium">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs mr-2"></i>
                                </button>
                            </div>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-home mr-2"></i>
                                        الصفحة الرئيسية
                                    </a>
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>
                                        البروفايل
                                    </a>
                                    <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>
                                        الإعدادات
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            تسجيل الخروج
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <span class="absolute top-0 bottom-0 left-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                            <i class="fas fa-times cursor-pointer"></i>
                        </span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <span class="absolute top-0 bottom-0 left-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                            <i class="fas fa-times cursor-pointer"></i>
                        </span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mx-6 mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('warning') }}</span>
                        <span class="absolute top-0 bottom-0 left-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                            <i class="fas fa-times cursor-pointer"></i>
                        </span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

