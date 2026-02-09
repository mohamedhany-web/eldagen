<?php

namespace App\Services;

use App\Contracts\NotificationDispatcherInterface;
use App\Jobs\SendBulkNotificationsJob;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

/**
 * خدمة إرسال الإشعارات (SOLID: Single Responsibility, Open/Closed)
 * مسؤولية واحدة: إرسال الإشعارات مع دعم الطابور للتوسع عند عدد كبير من المستلمين
 */
class NotificationDispatcherService implements NotificationDispatcherInterface
{
    public function __construct(
        protected int $queueBulkThreshold,
        protected string $queueName
    ) {}

    /**
     * إرسال إشعار لمجموعة مستخدمين: فوق العتبة → Queue، تحتها → إدراج مباشر
     */
    public function sendToUsers(array $userIds, array $data): void
    {
        $userIds = array_unique(array_filter($userIds));

        if (empty($userIds)) {
            return;
        }

        if (count($userIds) >= $this->queueBulkThreshold) {
            SendBulkNotificationsJob::dispatch($userIds, $data)->onQueue($this->queueName);
            Log::info('Bulk notifications dispatched to queue', [
                'count' => count($userIds),
                'threshold' => $this->queueBulkThreshold,
            ]);
            return;
        }

        $this->insertNotificationsSync($userIds, $data);
    }

    /**
     * إدراج الإشعارات مباشرة (لأعداد صغيرة)
     */
    protected function insertNotificationsSync(array $userIds, array $data): void
    {
        $now = now();
        if (isset($data['data']) && is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        $rows = [];
        foreach ($userIds as $userId) {
            $rows[] = array_merge($data, [
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        if ($rows !== []) {
            Notification::insert($rows);
        }
    }
}
