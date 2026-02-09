@extends('layouts.app')

@section('title', 'إدارة الطلبات')
@section('header', 'إدارة الطلبات')

@section('content')
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- إجمالي الطلبات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الطلبات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-blue-600 dark:text-blue-400">جميع الطلبات المسجلة</span>
            </div>
        </div>

        <!-- في الانتظار -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">في الانتظار</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-yellow-600 dark:text-yellow-400">تحتاج مراجعة</span>
            </div>
        </div>

        <!-- مقبولة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مقبولة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['approved']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600 dark:text-green-400">تم الموافقة عليها</span>
            </div>
        </div>

        <!-- مرفوضة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مرفوضة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['rejected']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-red-600 dark:text-red-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-red-600 dark:text-red-400">تم رفضها</span>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة والطلبات -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- البحث والفلترة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">البحث والفلترة</h3>
            </div>
            <div class="p-6">
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">البحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="ابحث بالاسم أو الإيميل..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">طريقة الدفع</label>
                        <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">جميع الطرق</option>
                            <option value="code" {{ request('payment_method') == 'code' ? 'selected' : '' }}>كود التفعيل</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex-1">
                            <i class="fas fa-search mr-2"></i>
                            بحث
                        </button>
                        
                        <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- قائمة الطلبات -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الطلبات الحديثة</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $orders->total() }} طلب</span>
                </div>
            </div>
            <div class="p-6">
                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200 cursor-pointer">
                            <!-- أيقونة الحالة -->
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0
                                @if($order->status == 'pending') bg-yellow-100 dark:bg-yellow-900
                                @elseif($order->status == 'approved') bg-green-100 dark:bg-green-900
                                @else bg-red-100 dark:bg-red-900
                                @endif">
                                @if($order->status == 'pending')
                                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                                @elseif($order->status == 'approved')
                                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                @else
                                    <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                                @endif
                            </div>
                            
                            <!-- معلومات الطلب -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user?->name ?? '—' }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $order->user?->phone ?? '—' }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">{{ $order->course?->title ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->course?->academicYear?->name ?? '—' }} - {{ $order->course?->academicSubject?->name ?? '—' }}</p>
                                
                                <!-- طريقة الدفع والكود إن وجد -->
                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        @if($order->payment_method === 'code')
                                            <i class="fas fa-ticket-alt mr-1"></i>
                                            كود: <span class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $order->activationCode->code ?? '—' }}</span>
                                        @else
                                            <i class="fas fa-money-bill mr-1"></i>
                                            {{ number_format($order->amount) }} ج.م
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- حالة الطلب والإجراءات -->
                            <div class="flex flex-col gap-2 items-end">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($order->status == 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif">
                                    {{ $order->status_text }}
                                </span>
                                
                                <div class="flex gap-2 mt-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($order->status == 'pending')
                                        <form method="POST" action="{{ route('admin.orders.approve', $order) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" 
                                                    onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                                    onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- الصفحات -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد طلبات</h3>
                        <p class="text-gray-500 dark:text-gray-400">لم يتم تقديم أي طلبات بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- إحصائيات إضافية -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تحليل الطلبات</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- معدل القبول -->
                <div class="flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-percentage text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">معدل القبول</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>

                <!-- الطلبات هذا الشهر -->
                <div class="flex items-center gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">هذا الشهر</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \App\Models\Order::whereMonth('created_at', now()->month)->count() }} طلب جديد
                        </p>
                    </div>
                </div>

                <!-- متوسط قيمة الطلب -->
                <div class="flex items-center gap-4 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-coins text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">متوسط قيمة الطلب</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $stats['total'] > 0 ? number_format(\App\Models\Order::avg('amount')) : 0 }} ج.م
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection