<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير ولي الأمر - {{ config('seo.site_name', 'المنصة') }}</title>
    <style>
        body { font-family: xbriyaz, lateef, dejavusans, sans-serif; margin: 0; padding: 0; color: #1e293b; font-size: 11pt; line-height: 1.5; direction: rtl; text-align: right; }
        .cover { padding: 28px 0 32px; margin-bottom: 28px; border-bottom: 4px solid #1e3a5f; background: #f1f5f9; }
        .cover h1 { margin: 0; font-size: 26pt; font-weight: bold; color: #1e3a5f; letter-spacing: 0; }
        .cover .sub { margin-top: 10px; font-size: 16pt; color: #334155; font-weight: bold; }
        .cover .meta { margin-top: 14px; font-size: 10pt; color: #64748b; }
        .student-block { margin-bottom: 32px; page-break-inside: avoid; }
        .student-title { background: #1e3a5f; color: #fff; padding: 12px 18px; font-size: 15pt; font-weight: bold; margin-bottom: 14px; border-radius: 4px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 11pt; }
        .info-table td { padding: 8px 14px; border: 1px solid #cbd5e1; }
        .info-table td:first-child { width: 160px; background: #f1f5f9; font-weight: bold; color: #334155; }
        .section-title { background: #1e3a5f; color: #fff; padding: 10px 14px; font-size: 12pt; font-weight: bold; margin: 18px 0 0; border-radius: 4px 4px 0 0; }
        table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10pt; }
        table.report-table th { background: #334155; color: #fff; padding: 10px 12px; text-align: right; border: 1px solid #475569; font-weight: bold; }
        table.report-table td { padding: 8px 12px; border: 1px solid #e2e8f0; }
        table.report-table tr:nth-child(even) { background: #f8fafc; }
        table.report-table thead tr th { font-size: 11pt; }
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .stats-table td { width: 25%; text-align: center; padding: 14px 10px; background: #f1f5f9; border: 1px solid #e2e8f0; }
        .stat-box .num { font-size: 22pt; font-weight: bold; color: #1e3a5f; display: block; }
        .stat-box .label { font-size: 10pt; color: #475569; margin-top: 4px; }
        .footer { margin-top: 28px; padding: 14px 0; border-top: 2px solid #e2e8f0; text-align: center; font-size: 9pt; color: #64748b; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 9pt; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-yellow { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .badge-gray { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .report-table tbody tr:hover { background: #f1f5f9 !important; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body dir="rtl" style="font-family: xbriyaz; direction: rtl; text-align: right;">

<div class="cover">
    <h1>{{ config('seo.site_name', 'منصة الطارق في الرياضيات') }}</h1>
    <div class="sub">تقارير ولي الأمر</div>
    <div class="meta">تاريخ التقرير: {{ now()->format('Y-m-d H:i') }} &nbsp;|&nbsp; جوال ولي الأمر: {{ $phone }}</div>
</div>

@foreach($reports as $r)
@php $user = $r['user']; @endphp
<div class="student-block">
    <div class="student-title">{{ $user->name }}</div>

    <table class="info-table">
        <tr><td>السنة الدراسية</td><td>{{ $user->academicYear->name ?? '—' }}</td></tr>
        <tr><td>آخر دخول</td><td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—' }}</td></tr>
        <tr><td>الحالة</td><td>{{ $user->suspended_at ? 'موقوف' : 'نشط' }}</td></tr>
    </table>

    <table class="stats-table">
        <tr>
            <td><div class="stat-box"><span class="num">{{ $r['enrollments']->count() }}</span><span class="label">تسجيلات كورسات</span></div></td>
            <td><div class="stat-box"><span class="num">{{ $r['lessonCompletedCount'] }}</span><span class="label">دروس مكتملة</span></div></td>
            <td><div class="stat-box"><span class="num">{{ $r['examAttempts']->count() }}</span><span class="label">محاولات امتحانات</span></div></td>
            <td><div class="stat-box"><span class="num">{{ $r['orders']->count() }}</span><span class="label">طلبات</span></div></td>
        </tr>
    </table>

    <div class="section-title">تسجيلات الكورسات</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>الكورس</th>
                <th>الحالة</th>
                <th>التقدم %</th>
                <th>تاريخ التسجيل</th>
            </tr>
        </thead>
        <tbody>
            @forelse($r['enrollments'] as $e)
            <tr>
                <td>{{ $e->course->title ?? '—' }}</td>
                <td><span class="badge badge-{{ $e->status === 'active' ? 'green' : ($e->status === 'pending' ? 'yellow' : 'gray') }}">{{ $e->status_text }}</span></td>
                <td>{{ $e->progress ?? 0 }}%</td>
                <td>{{ $e->enrolled_at ? $e->enrolled_at->format('Y-m-d') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center; color:#64748b;">لا يوجد تسجيلات</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">تقدم الدروس (آخر {{ $r['lessonProgressDetails']->count() }} سجل)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>الدرس</th>
                <th>الكورس</th>
                <th>مكتمل</th>
                <th>وقت المشاهدة</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($r['lessonProgressDetails'] as $lp)
            <tr>
                <td>{{ $lp->lesson->title ?? '—' }}</td>
                <td>{{ $lp->lesson->course->title ?? '—' }}</td>
                <td>{{ $lp->is_completed ? 'نعم' : '—' }}</td>
                <td>{{ $lp->watch_time ? round($lp->watch_time / 60) . ' د' : '—' }}</td>
                <td>{{ $lp->completed_at ? $lp->completed_at->format('Y-m-d') : ($lp->updated_at ? $lp->updated_at->format('Y-m-d') : '—') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#64748b;">لا يوجد سجلات</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">محاولات الامتحانات</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>الامتحان</th>
                <th>الدرجة / النسبة</th>
                <th>تاريخ التسليم</th>
            </tr>
        </thead>
        <tbody>
            @forelse($r['examAttempts'] as $a)
            <tr>
                <td>{{ $a->exam->title ?? '—' }}</td>
                <td>{{ $a->score ?? '—' }} / {{ $a->percentage ? round($a->percentage, 1) . '%' : '—' }}</td>
                <td>{{ $a->submitted_at ? $a->submitted_at->format('Y-m-d H:i') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center; color:#64748b;">لا توجد محاولات</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">الطلبات</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>الكورس</th>
                <th>المبلغ</th>
                <th>الحالة</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($r['orders'] as $o)
            <tr>
                <td>{{ $o->course->title ?? '—' }}</td>
                <td>{{ $o->amount ?? '—' }}</td>
                <td><span class="badge {{ $o->status === 'approved' ? 'badge-green' : ($o->status === 'pending' ? 'badge-yellow' : 'badge-gray') }}">{{ $o->status ?? '—' }}</span></td>
                <td>{{ $o->created_at ? $o->created_at->format('Y-m-d H:i') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center; color:#64748b;">لا توجد طلبات</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach

<div class="footer">
    {{ config('seo.site_name', 'المنصة') }} — تقرير ولي الأمر — تم الإنشاء في {{ now()->format('Y-m-d H:i') }}
</div>

</body>
</html>
