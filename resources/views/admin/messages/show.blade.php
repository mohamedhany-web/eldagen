@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('تفاصيل الرسالة') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('عرض تفاصيل الرسالة المرسلة') }}</p>
            </div>
            <a href="{{ route('admin.messages.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                {{ __('العودة') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- تفاصيل الرسالة -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('محتوى الرسالة') }}
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($message->status_color === 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($message->status_color === 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @elseif($message->status_color === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endif">
                            {{ $message->status_text }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <!-- معاينة الرسالة كما تظهر في الواتساب -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-whatsapp text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-green-700 dark:text-green-300 mb-1">
                                    {{ __('منصة مستر طارق الداجن') }}
                                </div>
                                <div class="text-gray-900 dark:text-white text-sm whitespace-pre-wrap leading-relaxed">
                                    {{ $message->message }}
                                </div>
                                <div class="flex items-center justify-between mt-3">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $message->sent_at ? $message->sent_at->format('d/m/Y H:i') : __('لم يتم الإرسال') }}
                                    </div>
                                    @if($message->status === 'sent')
                                        <div class="text-green-600 dark:text-green-400">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    @elseif($message->status === 'delivered')
                                        <div class="text-blue-600 dark:text-blue-400">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                    @elseif($message->status === 'read')
                                        <div class="text-blue-600 dark:text-blue-400">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($message->error_message)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 ml-2 mt-0.5"></i>
                                <div>
                                    <div class="text-sm font-medium text-red-700 dark:text-red-300 mb-1">
                                        {{ __('خطأ في الإرسال') }}:
                                    </div>
                                    <div class="text-red-600 dark:text-red-400 text-sm">
                                        {{ $message->error_message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="space-y-6">
            <!-- معلومات المستلم -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('معلومات المستلم') }}
                </h3>
                
                @if($message->user)
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 dark:text-primary-400 font-medium text-lg">
                                {{ substr($message->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="mr-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $message->user->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $message->user->role === 'student' ? __('طالب') : __('ولي أمر') }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('رقم الهاتف') }}:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $message->phone_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('نوع الرسالة') }}:</span>
                        <span class="text-gray-900 dark:text-white">{{ $message->type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('تاريخ الإنشاء') }}:</span>
                        <span class="text-gray-900 dark:text-white">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($message->sent_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('تاريخ الإرسال') }}:</span>
                            <span class="text-gray-900 dark:text-white">{{ $message->sent_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($message->delivered_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('تاريخ التسليم') }}:</span>
                            <span class="text-gray-900 dark:text-white">{{ $message->delivered_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('الإجراءات') }}
                </h3>
                
                <div class="space-y-3">
                    @if($message->status === 'failed')
                        <form action="{{ route('admin.messages.resend', $message) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('{{ __('هل تريد إعادة إرسال هذه الرسالة؟') }}')"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-redo ml-2"></i>
                                {{ __('إعادة الإرسال') }}
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.messages.destroy', $message) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('{{ __('هل تريد حذف هذه الرسالة؟') }}')"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash ml-2"></i>
                            {{ __('حذف الرسالة') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- البيانات التقنية -->
            @if($message->response_data)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('البيانات التقنية') }}
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($message->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
