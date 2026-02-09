@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">طلباتي</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">تتبع حالة طلباتك وعمليات الشراء</p>
                    </div>
                    <a href="{{ route('catalog.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        كورسات
                    </a>
                </div>
            </div>
        </div>

        <!-- الطلبات -->
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $order->course->title }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $order->status_text }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    <div class="flex items-center gap-4">
                                        <span>{{ $order->course->academicYear->name }} - {{ $order->course->academicSubject->name }}</span>
                                        <span>•</span>
                                        <span>{{ $order->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">المبلغ:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            @if($order->payment_method == 'code') — كود
                                            @else {{ number_format($order->amount) }} ج.م
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">طريقة الدفع:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            @if($order->payment_method == 'code')
                                                كود التفعيل
                                                @if($order->activationCode)
                                                    <span class="font-mono text-indigo-600">({{ $order->activationCode->code }})</span>
                                                @endif
                                            @elseif($order->payment_method == 'bank_transfer') تحويل بنكي
                                            @elseif($order->payment_method == 'cash') نقدي
                                            @else أخرى
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">تاريخ الطلب:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    @if($order->approved_at)
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">تاريخ الموافقة:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $order->approved_at->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($order->notes)
                                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">ملاحظات: </span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $order->notes }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col gap-2 ml-4">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                                    عرض التفاصيل
                                </a>
                                
                                @if($order->status == 'approved')
                                    <a href="{{ route('my-courses.show', $order->course) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                                        ادخل للكورس
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- الصفحات -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                    <p class="text-lg font-medium">لا توجد طلبات</p>
                    <p class="text-sm mt-2 mb-4">لم تقم بتقديم أي طلبات بعد</p>
                    <a href="{{ route('catalog.index') }}" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        كورسات
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection









