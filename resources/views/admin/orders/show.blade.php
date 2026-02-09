@extends('layouts.app')

@section('title', 'تفاصيل الطلب #' . $order->id)
@section('header', 'تفاصيل الطلب #' . $order->id)

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تفاصيل الطلب #{{ $order->id }}</h1>
                <p class="text-gray-600 mt-1">{{ $order->created_at->format('d/m/Y - H:i') }}</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة للطلبات
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- تفاصيل الطلب -->
        <div class="lg:col-span-2 space-y-6">
            <!-- معلومات الطالب -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">معلومات الطالب</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">الاسم</label>
                        <div class="text-lg font-medium text-gray-900">{{ $order->user?->name ?? '—' }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
                        <div class="text-lg font-medium text-gray-900">{{ $order->user?->phone ?? '—' }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                        <div class="text-lg font-medium text-gray-900">{{ $order->user?->email ?? '—' }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التسجيل</label>
                        <div class="text-lg font-medium text-gray-900">{{ $order->user?->created_at?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <!-- معلومات الكورس -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">معلومات الكورس</h2>
                
                <div class="flex gap-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        @if($order->course?->image)
                            <img src="{{ storage_url($order->course->image) }}" alt="{{ $order->course->title ?? '' }}" 
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <i class="fas fa-play-circle text-white text-2xl"></i>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $order->course?->title ?? '—' }}</h3>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $order->course?->academicYear?->name ?? '—' }} - {{ $order->course?->academicSubject?->name ?? '—' }}
                        </p>
                        @if($order->course?->description)
                            <p class="text-sm text-gray-700">
                                {{ Str::limit($order->course->description, 150) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- تفاصيل الدفع -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">تفاصيل الدفع</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">المبلغ</label>
                        <div class="text-xl font-bold text-gray-900">{{ number_format($order->amount) }} ج.م</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">طريقة الدفع</label>
                        <div class="text-lg font-medium text-gray-900">
                            @if($order->payment_method == 'code')
                                كود التفعيل
                                @if($order->activationCode)
                                    <span class="font-mono text-indigo-600">({{ $order->activationCode->code }})</span>
                                @endif
                            @elseif($order->payment_method == 'bank_transfer') تحويل بنكي
                            @elseif($order->payment_method == 'cash') نقدي
                            @else أخرى
                            @endif
                        </div>
                    </div>
                    @if($order->payment_method == 'code' && $order->activationCode)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">الكود المستخدم</label>
                        <div class="text-lg font-mono font-bold text-indigo-600">{{ $order->activationCode->code }}</div>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ الطلب</label>
                        <div class="text-lg font-medium text-gray-900">{{ $order->created_at->format('d/m/Y - H:i') }}</div>
                    </div>
                    
                    @if($order->approved_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ المراجعة</label>
                            <div class="text-lg font-medium text-gray-900">{{ $order->approved_at->format('d/m/Y - H:i') }}</div>
                        </div>
                    @endif
                </div>

                @if($order->notes)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات الطالب</label>
                        <div class="p-3 bg-gray-50 rounded-lg text-gray-900">
                            {{ $order->notes }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- صورة الإيصال -->
            @if($order->payment_proof)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">إيصال الدفع</h2>
                    
                    <div class="text-center">
                        <img src="{{ storage_url($order->payment_proof) }}" 
                             alt="إيصال الدفع" 
                             class="max-w-full h-auto rounded-lg shadow-md cursor-pointer"
                             onclick="openImageModal(this.src)">
                        <p class="text-sm text-gray-500 mt-2">اضغط على الصورة لعرضها بحجم أكبر</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- حالة الطلب والإجراءات -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">حالة الطلب</h2>
                
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto mb-3 rounded-full flex items-center justify-center
                        @if($order->status == 'pending') bg-yellow-100
                        @elseif($order->status == 'approved') bg-green-100
                        @else bg-red-100
                        @endif">
                        <i class="fas 
                            @if($order->status == 'pending') fa-clock text-yellow-600 text-2xl
                            @elseif($order->status == 'approved') fa-check text-green-600 text-2xl
                            @else fa-times text-red-600 text-2xl
                            @endif"></i>
                    </div>
                    
                    <div class="text-xl font-bold
                        @if($order->status == 'pending') text-yellow-600
                        @elseif($order->status == 'approved') text-green-600
                        @else text-red-600
                        @endif">
                        {{ $order->status_text }}
                    </div>
                </div>

                @if($order->status == 'pending')
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.orders.approve', $order) }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors"
                                    onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس للطالب تلقائياً.')">
                                <i class="fas fa-check mr-2"></i>
                                الموافقة على الطلب
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.orders.reject', $order) }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium transition-colors"
                                    onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                <i class="fas fa-times mr-2"></i>
                                رفض الطلب
                            </button>
                        </form>
                    </div>
                @elseif($order->status == 'approved')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            تمت الموافقة على الطلب وتم تفعيل الكورس للطالب.
                        </p>
                    </div>
                @else
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            تم رفض هذا الطلب.
                        </p>
                    </div>
                @endif

                @if($order->approver)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500">تمت المراجعة بواسطة:</p>
                        <p class="font-medium text-gray-900">{{ $order->approver?->name ?? '—' }}</p>
                        @if($order->approved_at)
                            <p class="text-sm text-gray-500">{{ $order->approved_at->format('d/m/Y - H:i') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal لعرض الصورة -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="max-w-4xl max-h-full p-4">
        <img id="modalImage" src="" alt="إيصال الدفع" class="max-w-full max-h-full object-contain">
    </div>
</div>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
}
</script>
@endsection









