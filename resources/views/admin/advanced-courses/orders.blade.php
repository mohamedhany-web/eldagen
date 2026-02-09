@extends('layouts.app')

@section('title', 'طلبات الكورس')
@section('header', 'طلبات الكورس: ' . $advancedCourse->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.show', $advancedCourse) }}" class="hover:text-primary-600">{{ $advancedCourse->title }}</a>
                <span class="mx-2">/</span>
                <span>الطلبات</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-list ml-2"></i>
                جميع الطلبات
            </a>
            <a href="{{ route('admin.advanced-courses.show', $advancedCourse) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للكورس
            </a>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center ml-4">
                    <i class="fas fa-graduation-cap text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $advancedCourse->title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $advancedCourse->academicYear->name ?? 'غير محدد' }} - {{ $advancedCourse->academicSubject->name ?? 'غير محدد' }}
                    </p>
                </div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600">{{ $orders->total() }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي الطلبات</div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الطلبات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orders->where('status', 'pending')->count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">معلقة</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orders->where('status', 'approved')->count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">مقبولة</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-red-100 dark:bg-red-900">
                    <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orders->where('status', 'rejected')->count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">مرفوضة</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orders->total() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الطلبات -->
    @if($orders->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">طلبات التسجيل</h4>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">طريقة الدفع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">تاريخ الطلب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                            <span class="text-primary-600 dark:text-primary-400 font-medium">
                                                {{ substr($order->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($order->payment_method == 'whatsapp') واتساب
                                        @elseif($order->payment_method == 'bank_transfer') تحويل بنكي
                                        @elseif($order->payment_method == 'cash') كاش
                                        @else {{ $order->payment_method }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($order->amount, 2) }} ج.م</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status == 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $order->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status == 'pending')
                                            <form action="{{ route('admin.orders.approve', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('هل تريد الموافقة على هذا الطلب؟')"
                                                        class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('هل تريد رفض هذا الطلب؟')"
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- التصفح -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">لم يتم تقديم أي طلبات تسجيل لهذا الكورس بعد</p>
            <a href="{{ route('admin.orders.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-list ml-2"></i>
                عرض جميع الطلبات
            </a>
        </div>
    @endif
</div>
@endsection
