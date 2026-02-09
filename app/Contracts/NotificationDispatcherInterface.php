<?php

namespace App\Contracts;

/**
 * عقد إرسال الإشعارات (SOLID: Interface Segregation, Dependency Inversion)
 * يفصل منطق الإرسال عن النموذج لتمكين الطوابير والتوسع
 */
interface NotificationDispatcherInterface
{
    /**
     * إرسال إشعار لمجموعة مستخدمين (مزامن أو عبر Queue حسب العدد)
     */
    public function sendToUsers(array $userIds, array $data): void;
}
