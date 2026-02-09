<?php

namespace App\Contracts;

/**
 * عقد إحصائيات لوحة التحكم (SOLID: Interface Segregation, Dependency Inversion)
 * مسؤولية واحدة: توفير إحصائيات لوحة التحكم مع دعم التخزين المؤقت للتوسع
 */
interface DashboardStatsContract
{
    /**
     * إحصائيات لوحة تحكم الإدارة (مع cache)
     */
    public function getAdminStats(): array;

    /**
     * إحصائيات شهرية للإدارة
     */
    public function getAdminMonthlyStats(): array;

    /**
     * إبطال cache الإحصائيات (بعد إنشاء/تحديث/حذف مستخدم أو كورس)
     */
    public function forgetAdminStatsCache(): void;
}
