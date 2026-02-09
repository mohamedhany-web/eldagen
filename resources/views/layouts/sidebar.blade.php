<div class="flex flex-col h-full">
    <!-- شعار المنصة — على الهاتف هامش علوي لزر الإغلاق -->
    <div class="p-5 pt-14 lg:pt-5 border-b border-gray-200 dark:border-gray-700/80 bg-white/80 dark:bg-gray-800/80">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-calculator text-white text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-lg font-black bg-clip-text text-transparent truncate leading-tight" style="background: linear-gradient(to right, #667eea, #764ba2); -webkit-background-clip: text; background-clip: text;">مستر طارق الداجن</h2>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-0.5">منصة الرياضيات</p>
            </div>
        </div>
    </div>

    <!-- القائمة الرئيسية -->
    <nav class="flex-1 p-3 overflow-y-auto sidebar scrollbar-custom">
        <ul class="space-y-1.5">
            <!-- لوحة التحكم -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="group sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl relative
                          {{ request()->routeIs('dashboard') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300' }}">
                    <span class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-[60%] min-h-[24px] rounded-l-full transition-opacity duration-300 {{ request()->routeIs('dashboard') ? 'opacity-0' : 'opacity-0 group-hover:opacity-100' }}" style="background: linear-gradient(to bottom, #667eea, #764ba2);"></span>
                    <span class="sidebar-icon-box relative {{ request()->routeIs('dashboard') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}">
                        <i class="fas fa-chart-line text-sm"></i>
                        @if(request()->routeIs('dashboard'))
                        <span class="absolute -top-0.5 -left-0.5 w-1.5 h-1.5 rounded-full bg-white ring-2 ring-[#667eea]"></span>
                        @endif
                    </span>
                    <span class="font-semibold">لوحة التحكم</span>
                </a>
            </li>

            @if(auth()->user()->isAdmin())
                <!-- إدارة النظام -->
                <li x-data="{ open: {{ request()->routeIs('admin.users*', 'admin.orders*', 'admin.student-sessions.*', 'admin.notifications.*', 'admin.activity-log*', 'admin.statistics*', 'admin.maintenance*', 'admin.suspended-students*', 'admin.permissions*') ? 'true' : 'false' }} }" class="group/menu">
                    <button @click="open = !open" 
                            class="sidebar-nav-link flex items-center justify-between w-full px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-cogs text-sm"></i></span>
                            <span class="font-semibold">إدارة النظام</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300 text-gray-400 group-hover/menu:text-indigo-600" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <ul x-show="open" x-transition class="mt-1 mr-4 space-y-0.5 border-r-2 border-indigo-200 dark:border-indigo-800/50 pr-3 mr-1">
                        @if(auth()->user()->hasPermission('admin.permissions'))
                        <li><a href="{{ route('admin.permissions.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.permissions*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-user-shield w-4 text-center"></i>الصلاحيات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.users'))
                        <li><a href="{{ route('admin.users') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.users*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-users w-4 text-center"></i>إدارة المستخدمين</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.student-sessions'))
                        <li><a href="{{ route('admin.student-sessions.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.student-sessions.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-mobile-alt w-4 text-center"></i>تسجيلات دخول الطلاب</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.orders'))
                        <li><a href="{{ route('admin.orders.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.orders.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-shopping-cart w-4 text-center"></i>الطلبات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.activation-codes'))
                        <li><a href="{{ route('admin.activation-codes.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.activation-codes.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-ticket-alt w-4 text-center"></i>أكواد التفعيل</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.notifications'))
                        <li><a href="{{ route('admin.notifications.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.notifications.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-bell w-4 text-center"></i>إرسال الإشعارات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.activity-log'))
                        <li><a href="{{ route('admin.activity-log') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400"><i class="fas fa-history w-4 text-center"></i>سجل النشاطات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.statistics'))
                        <li><a href="{{ route('admin.statistics') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400"><i class="fas fa-chart-pie w-4 text-center"></i>الإحصائيات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.maintenance'))
                        <li><a href="{{ route('admin.maintenance.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.maintenance*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-tools w-4 text-center"></i>الصيانة</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.suspended-students'))
                        <li><a href="{{ route('admin.suspended-students.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.suspended-students*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-user-slash w-4 text-center"></i>الطلاب المخالفون</a></li>
                        @endif
                    </ul>
                </li>

                <!-- إدارة المحتوى -->
                <li x-data="{ open: false }" class="group/menu">
                    <button @click="open = !open" 
                            class="sidebar-nav-link flex items-center justify-between w-full px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-folder-open text-sm"></i></span>
                            <span class="font-semibold">إدارة المحتوى</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300 text-gray-400 group-hover/menu:text-indigo-600" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <ul x-show="open" x-transition class="mt-1 mr-4 space-y-0.5 border-r-2 border-indigo-200 dark:border-indigo-800/50 pr-3 mr-1">
                        <li class="pt-2 pb-1">
                            <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 px-4 py-1 uppercase tracking-widest">النظام الأكاديمي</div>
                        </li>
                        @if(auth()->user()->hasPermission('admin.academic-years'))
                        <li><a href="{{ route('admin.academic-years.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.academic-years.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-calendar-alt w-4 text-center"></i>السنوات الدراسية</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.academic-subjects'))
                        <li><a href="{{ route('admin.academic-subjects.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.academic-subjects.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-book w-4 text-center"></i>المواد الدراسية</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.advanced-courses'))
                        <li><a href="{{ route('admin.advanced-courses.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.advanced-courses.*', 'admin.courses.lessons.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-graduation-cap w-4 text-center"></i>الكورسات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.enrollments'))
                        <li><a href="{{ route('admin.enrollments.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.enrollments.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-user-graduate w-4 text-center"></i>تسجيل الطلاب</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.exams'))
                        <li><a href="{{ route('admin.exams.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.exams.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-clipboard-check w-4 text-center"></i>الامتحانات</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('admin.question-bank'))
                        <li><a href="{{ route('admin.question-bank.index') }}" class="sidebar-sub-link flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.question-bank.*', 'admin.question-categories.*') ? 'sidebar-nav-item-active font-medium' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><i class="fas fa-database w-4 text-center"></i>بنك الأسئلة</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(auth()->user()->isTeacher())
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-book text-sm"></i></span><span class="font-semibold">كورساتي</span></a></li>
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-users text-sm"></i></span><span class="font-semibold">صفوفي</span></a></li>
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-tasks text-sm"></i></span><span class="font-semibold">الواجبات</span></a></li>
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-clipboard-check text-sm"></i></span><span class="font-semibold">الاختبارات</span></a></li>
            @endif

            @if(auth()->user()->isStudent())
                <li><a href="{{ route('catalog.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('catalog.*') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('catalog.*') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-graduation-cap text-sm"></i></span><span class="font-semibold">كورسات</span></a></li>
                <li><a href="{{ route('orders.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('orders.*') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('orders.*') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-shopping-cart text-sm"></i></span><span class="font-semibold">طلباتي</span></a></li>
                <li><a href="{{ route('my-courses.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('my-courses.*') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('my-courses.*') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-book-open text-sm"></i></span><span class="font-semibold">كورساتي</span></a></li>
                <li><a href="{{ route('student.exams.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('student.exams.*') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('student.exams.*') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-clipboard-check text-sm"></i></span><span class="font-semibold">الامتحانات</span></a></li>
                <li><a href="{{ route('notifications') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('notifications') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('notifications') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-bell text-sm"></i></span><span class="font-semibold">الإشعارات</span></a></li>
                <li><a href="{{ route('profile') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('profile') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('profile') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-user text-sm"></i></span><span class="font-semibold">البروفايل</span></a></li>
                <li><a href="{{ route('settings') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('settings') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-cog text-sm"></i></span><span class="font-semibold">الإعدادات</span></a></li>
            @endif

            @if(auth()->user()->isParent())
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-child text-sm"></i></span><span class="font-semibold">أطفالي</span></a></li>
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-chart-bar text-sm"></i></span><span class="font-semibold">تقارير الأداء</span></a></li>
                <li><a href="#" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400"><span class="sidebar-icon-box bg-indigo-100 dark:bg-indigo-900/30"><i class="fas fa-comments text-sm"></i></span><span class="font-semibold">التواصل</span></a></li>
            @endif

            <li class="my-2"><div class="h-px bg-gradient-to-l from-transparent via-gray-200 dark:via-gray-600 to-transparent"></div></li>

            @if(auth()->user()->isAdmin() && auth()->user()->hasPermission('admin.messages'))
                <li><a href="{{ route('admin.messages.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.messages.*') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('admin.messages.*') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-chart-line text-sm"></i></span><span class="font-semibold">التقارير</span></a></li>
            @endif

            <li><a href="{{ route('calendar') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('calendar') ? 'sidebar-nav-item-active' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-600 dark:hover:text-indigo-400' }}"><span class="sidebar-icon-box {{ request()->routeIs('calendar') ? '' : 'bg-indigo-100 dark:bg-indigo-900/30' }}"><i class="fas fa-calendar-alt text-sm"></i></span><span class="font-semibold">التقويم</span></a></li>
        </ul>
    </nav>

    <!-- معلومات المستخدم — أفاتار أزرق–نيلي -->
    <div class="p-3 border-t border-gray-200 dark:border-gray-700/80 bg-white/80 dark:bg-gray-800/80">
        <div class="flex items-center gap-3 p-3 rounded-2xl transition-all duration-300 hover:shadow-md border border-indigo-200/50 dark:border-indigo-800/30" style="background: linear-gradient(to right, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 truncate mt-0.5">{{ auth()->user()->phone }}</p>
            </div>
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 ring-2 ring-white dark:ring-gray-800 animate-pulse" style="background: #667eea;" title="متصل"></span>
        </div>
    </div>
</div>
