@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">التقويم الأكاديمي</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">امتحاناتك ومواعيد الكورسات المسجل فيها</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- التقويم -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                    <!-- هيدر التقويم مع تنقل الشهور -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="الشهر التالي">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white min-w-[180px] text-center">
                                {{ $currentMonth->translatedFormat('F Y') }}
                            </h2>
                            <a href="{{ route('calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="الشهر السابق">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </div>
                        <a href="{{ route('calendar') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">الشهر الحالي</a>
                    </div>

                    <!-- شبكة التقويم -->
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <!-- أيام الأسبوع -->
                        @foreach(['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'] as $day)
                            <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded">
                                {{ $day }}
                            </div>
                        @endforeach

                        <!-- أيام الشهر -->
                        @foreach($weeks as $cell)
                            <div class="p-2 min-h-[4.5rem] border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-default relative
                                {{ $cell['is_today'] ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-600' : '' }}">
                                @if($cell['day'] !== null)
                                    <div class="text-sm {{ $cell['is_today'] ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $cell['day'] }}
                                    </div>
                                    @if($cell['event_count'] > 0)
                                        <div class="mt-1 flex flex-wrap gap-0.5">
                                            @for($i = 0; $i < min($cell['event_count'], 3); $i++)
                                                <div class="w-2 h-2 bg-red-500 rounded-full" title="{{ $cell['event_count'] }} حدث"></div>
                                            @endfor
                                            @if($cell['event_count'] > 3)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">+{{ $cell['event_count'] - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- الأحداث القادمة -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الأحداث القادمة</h3>

                    <div class="space-y-3">
                        @forelse($events as $event)
                            <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-3 h-3 rounded-full flex-shrink-0
                                        @if($event->color == 'red') bg-red-500
                                        @elseif($event->color == 'blue') bg-blue-500
                                        @elseif($event->color == 'green') bg-green-500
                                        @else bg-gray-500
                                        @endif"></div>
                                    @if(!empty($event->url))
                                        <a href="{{ $event->url }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 truncate block">
                                            {{ $event->title }}
                                        </a>
                                    @else
                                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate block">{{ $event->title }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $event->date->format('d/m/Y') }}
                                    @if($event->date->format('H:i') !== '00:00')
                                        — {{ $event->date->format('H:i') }}
                                    @endif
                                </div>
                                @if(!empty($event->course_title))
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event->course_title }}</div>
                                @endif
                                <div class="text-xs mt-1
                                    @if($event->type == 'exam') text-red-600 dark:text-red-400
                                    @elseif($event->type == 'lesson') text-blue-600 dark:text-blue-400
                                    @elseif($event->type == 'review') text-green-600 dark:text-green-400
                                    @else text-gray-600 dark:text-gray-400
                                    @endif">
                                    @if($event->type == 'exam') امتحان
                                    @elseif($event->type == 'lesson') درس
                                    @elseif($event->type == 'review') مراجعة
                                    @else حدث
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">لا توجد أحداث قادمة في الكورسات المسجل فيها.</p>
                        @endforelse
                    </div>
                </div>

                <!-- إحصائيات بناءً على البيانات الفعلية -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">هذا الشهر</h3>
                    <div class="space-y-3">
                        @php
                            $monthStart = $currentMonth->copy()->startOfMonth();
                            $monthEnd = $currentMonth->copy()->endOfMonth();
                            $eventsThisMonth = $events->filter(function ($e) use ($monthStart, $monthEnd) {
                                return $e->date->between($monthStart, $monthEnd);
                            });
                            $examsCount = $eventsThisMonth->where('type', 'exam')->count();
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">الامتحانات</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ $examsCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">إجمالي الأحداث</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $eventsThisMonth->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
