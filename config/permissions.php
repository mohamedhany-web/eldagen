<?php

return [
    /*
    | صلاحيات لوحة الإدارة — تظهر للمستخدم حسب الدور أو التخصيص.
    | عند اختيار دور "إداري" يمكن تخصيص الصلاحيات؛ إن تُركت فارغة = جميع الصلاحيات.
    */
    'admin' => [
        'إدارة النظام' => [
            'admin.dashboard' => 'لوحة التحكم',
            'admin.permissions' => 'الصلاحيات',
            'admin.users' => 'إدارة المستخدمين',
            'admin.student-sessions' => 'تسجيلات دخول الطلاب',
            'admin.orders' => 'الطلبات',
            'admin.activation-codes' => 'أكواد التفعيل',
            'admin.notifications' => 'إرسال الإشعارات',
            'admin.activity-log' => 'سجل النشاطات',
            'admin.statistics' => 'الإحصائيات',
            'admin.maintenance' => 'الصيانة',
            'admin.suspended-students' => 'الطلاب المخالفون',
        ],
        'إدارة المحتوى' => [
            'admin.academic-years' => 'السنوات الدراسية',
            'admin.academic-subjects' => 'المواد الدراسية',
            'admin.advanced-courses' => 'الكورسات',
            'admin.enrollments' => 'تسجيل الطلاب',
            'admin.exams' => 'الامتحانات',
            'admin.question-bank' => 'بنك الأسئلة',
            'admin.question-categories' => 'تصنيفات الأسئلة',
        ],
        'التقارير' => [
            'admin.messages' => 'التقارير',
        ],
    ],

    /*
    | ربط أسماء المسارات بمفتاح الصلاحية (للمسارات الفرعية مثل الدروس والأقسام)
    */
    'route_to_permission' => [
        'admin.courses.lessons' => 'admin.advanced-courses',
        'admin.courses.sections' => 'admin.advanced-courses',
    ],

    /*
    | مفتاح الصلاحية من اسم المسار: admin.users.edit -> admin.users
    */
    'route_prefixes' => [
        'admin.dashboard',
        'admin.permissions',
        'admin.users',
        'admin.student-sessions',
        'admin.orders',
        'admin.activation-codes',
        'admin.notifications',
        'admin.activity-log',
        'admin.statistics',
        'admin.maintenance',
        'admin.suspended-students',
        'admin.academic-years',
        'admin.academic-subjects',
        'admin.advanced-courses',
        'admin.enrollments',
        'admin.exams',
        'admin.question-bank',
        'admin.question-categories',
        'admin.messages',
    ],
];
