@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- تفاصيل الكورس -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <!-- صورة الكورس -->
                    <div class="h-64 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center relative">
                        @if($advancedCourse->image)
                            <img src="{{ storage_url($advancedCourse->image) }}" alt="{{ $advancedCourse->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-play-circle text-white text-8xl"></i>
                        @endif
                        
                        <!-- شارة المستوى -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($advancedCourse->level == 'beginner') bg-green-100 text-green-800
                                @elseif($advancedCourse->level == 'intermediate') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $advancedCourse->level_badge['text'] ?? 'مبتدئ' }}
                            </span>
                        </div>
                    </div>

                    <!-- محتوى الكورس -->
                    <div class="p-6">
                        <div class="mb-4">
                            <nav class="text-sm text-gray-500 dark:text-gray-400">
                                <a href="{{ route('academic-years') }}" class="hover:text-blue-600">السنوات الدراسية</a>
                                <span class="mx-2">/</span>
                                <a href="{{ route('academic-years.subjects', $advancedCourse->academicYear) }}" class="hover:text-blue-600">{{ $advancedCourse->academicYear->name }}</a>
                                <span class="mx-2">/</span>
                                <a href="{{ route('subjects.courses', $advancedCourse->academicSubject) }}" class="hover:text-blue-600">{{ $advancedCourse->academicSubject->name }}</a>
                            </nav>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $advancedCourse->title }}</h1>
                        
                        <div class="prose max-w-none dark:prose-invert mb-6">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $advancedCourse->description }}</p>
                        </div>

                        <!-- معلومات الكورس -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            @if($advancedCourse->duration)
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <i class="fas fa-clock text-blue-600 text-xl mb-2"></i>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">المدة</div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $advancedCourse->duration }}</div>
                                </div>
                            @endif
                            @if($advancedCourse->lessons_count)
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <i class="fas fa-video text-green-600 text-xl mb-2"></i>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">الدروس</div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $advancedCourse->lessons_count }} درس</div>
                                </div>
                            @endif
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <i class="fas fa-signal text-purple-600 text-xl mb-2"></i>
                                <div class="text-sm text-gray-600 dark:text-gray-400">المستوى</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $advancedCourse->level_badge['text'] ?? 'مبتدئ' }}</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <i class="fas fa-users text-orange-600 text-xl mb-2"></i>
                                <div class="text-sm text-gray-600 dark:text-gray-400">الطلاب</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $advancedCourse->enrollments_count ?? 0 }}</div>
                            </div>
                        </div>

                        <!-- محتوى الكورس -->
                        @if($advancedCourse->syllabus)
                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">محتوى الكورس</h3>
                                <div class="prose max-w-none dark:prose-invert">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $advancedCourse->syllabus }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- بطاقة الشراء -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 sticky top-8">
                    <!-- السعر -->
                    <div class="text-center mb-6">
                        @if($advancedCourse->price && $advancedCourse->price > 0)
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($advancedCourse->price) }} <span class="text-lg text-gray-500">ج.م</span></div>
                        @else
                            <div class="text-3xl font-bold text-green-600">مجاني</div>
                        @endif
                    </div>

                    <!-- حالة التسجيل -->
                    @if($isEnrolled)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center gap-2 text-green-800">
                                <i class="fas fa-check-circle"></i>
                                <span class="font-medium">أنت مسجل في هذا الكورس</span>
                            </div>
                        </div>
                        <a href="#" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors text-center block">
                            ادخل للكورس
                        </a>
                    @elseif($existingOrder)
                        @if($existingOrder->status == 'pending')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center gap-2 text-yellow-800">
                                    <i class="fas fa-clock"></i>
                                    <span class="font-medium">طلبك قيد المراجعة</span>
                                </div>
                                <p class="text-sm text-yellow-700 mt-1">سيتم مراجعة طلبك والرد عليك قريباً</p>
                            </div>
                            <a href="{{ route('orders.show', $existingOrder) }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-4 rounded-lg font-medium transition-colors text-center block">
                                عرض حالة الطلب
                            </a>
                        @elseif($existingOrder->status == 'rejected')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center gap-2 text-red-800">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="font-medium">تم رفض طلبك</span>
                                </div>
                                <p class="text-sm text-red-700 mt-1">يمكنك تقديم طلب جديد</p>
                            </div>
                            <button onclick="toggleOrderForm()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                اطلب الآن
                            </button>
                        @endif
                    @else
                        <button onclick="toggleOrderForm()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                            @if($advancedCourse->price && $advancedCourse->price > 0)
                                اشتري الآن
                            @else
                                سجل مجاناً
                            @endif
                        </button>
                    @endif

                    <!-- نموذج الطلب -->
                    @if(!$isEnrolled && (!$existingOrder || $existingOrder->status == 'rejected'))
                        <div id="orderForm" class="hidden mt-6 border-t pt-6">
                            <form action="{{ route('courses.order', $advancedCourse) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">طريقة الدفع</label>
                                    <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">اختر طريقة الدفع</option>
                                        <option value="bank_transfer">تحويل بنكي</option>
                                        <option value="cash">نقدي</option>
                                        <option value="other">أخرى</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">صورة الإيصال</label>
                                    <input type="file" name="payment_proof" accept="image/*" required 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="text-xs text-gray-500 mt-1">ارفع صورة الإيصال أو الفاتورة</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ملاحظات (اختياري)</label>
                                    <textarea name="notes" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                              placeholder="أي ملاحظات إضافية..."></textarea>
                                </div>

                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                    إرسال الطلب
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOrderForm() {
    const form = document.getElementById('orderForm');
    form.classList.toggle('hidden');
}
</script>
@endsection


