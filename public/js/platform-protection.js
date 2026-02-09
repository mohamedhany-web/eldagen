/**
 * نظام حماية المنصة — تم تعطيل الشاشة السوداء "محتوى محمي" نهائياً.
 * يُحتفظ فقط بتهيئة اسم المستخدم وتذكير مغادرة صفحة المشاهدة إن وُجد.
 */

(function() {
    'use strict';

    // تهيئة اسم المستخدم للـ body (إن وُجد لاستخدامات أخرى)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Laravel && window.Laravel.user) {
            document.body.setAttribute('data-user-name', window.Laravel.user.name);
        }
    });

    // تذكير عند مغادرة صفحة مشاهدة الدرس فقط
    window.addEventListener('beforeunload', function(e) {
        if (window.location.pathname.includes('/watch')) {
            e.preventDefault();
            e.returnValue = 'هل تريد مغادرة الدرس؟ سيتم حفظ تقدمك.';
            return e.returnValue;
        }
    });

})();
