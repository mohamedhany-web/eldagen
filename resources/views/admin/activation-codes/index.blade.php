@extends('layouts.app')

@section('title', 'أكواد التفعيل')
@section('header', 'أكواد التفعيل')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- شريط التصدير -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">قائمة أكواد التفعيل</h2>
        <a href="{{ route('admin.activation-codes.export', request()->only(['course', 'status', 'search'])) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors shadow-lg shadow-emerald-500/25">
            <i class="fas fa-file-excel"></i>
            تحميل Excel
        </a>
    </div>

    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الأكواد</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
                <i class="fas fa-ticket-alt text-blue-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">متاحة</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['active']) }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مستخدمة</p>
                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($stats['used']) }}</p>
                </div>
                <i class="fas fa-user-check text-amber-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- توليد أكواد جديدة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">توليد أكواد جديدة</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.activation-codes.store') }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf
                <div class="min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الكورس</label>
                    <select name="advanced_course_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('advanced_course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                        @endforeach
                    </select>
                    @error('advanced_course_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">العدد</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 5) }}" min="1" max="500" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    @error('quantity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    توليد الأكواد
                </button>
            </form>
        </div>
    </div>

    <!-- فلترة وقائمة الأكواد -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">فلترة</h3>
            </div>
            <div class="p-6">
                <form method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الكورس</label>
                        <select name="course" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="">جميع الكورسات</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>{{ Str::limit($c->title, 30) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="">الكل</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>متاحة</option>
                            <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>مستخدمة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">بحث (كود أو طالب)</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="كود أو اسم طالب..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-center gap-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">قائمة الأكواد</h3>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $codes->total() }} كود</span>
                    <a href="{{ route('admin.activation-codes.export', request()->only(['course', 'status', 'search'])) }}"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-file-excel"></i>
                        Excel
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                @if($codes->count() > 0)
                    <table class="w-full text-right">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الكود</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الكورس</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الطالب</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">تاريخ الاستخدام</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($codes as $code)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $code->code }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($code->course->title ?? '-', 35) }}</td>
                                <td class="px-4 py-3">
                                    @if($code->status === 'used')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                            <i class="fas fa-user-check ml-1"></i>
                                            مستخدم
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            <i class="fas fa-check-circle ml-1"></i>
                                            متاح
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    @if($code->usedBy)
                                        {{ $code->usedBy->name }}
                                        @if($code->usedBy->phone)
                                            <span class="text-gray-500">({{ $code->usedBy->phone }})</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $code->used_at ? $code->used_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $code->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $codes->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-ticket-alt text-4xl mb-4"></i>
                        <p>لا توجد أكواد. قم بتوليد أكواد جديدة من النموذج أعلاه.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
