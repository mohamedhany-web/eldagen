<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * إرسال إشعارات جماعية عبر الطابور (توسع مع عدد كبير من الطلاب)
 * مسؤولية واحدة: إدراج دفعات من الإشعارات دون إرهاق الطلب الحالي
 */
class SendBulkNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    /**
     * حجم الدفعة للإدراج (لتقليل الذاكرة ووقت القفل)
     */
    protected const CHUNK_SIZE = 500;

    /**
     * @param array<int> $userIds
     * @param array<string, mixed> $data
     */
    public function __construct(
        public array $userIds,
        public array $data
    ) {
        $this->onQueue(config('performance.notifications.queue_name', 'default'));
    }

    public function handle(): void
    {
        $userIds = array_unique(array_filter($this->userIds));
        if ($userIds === []) {
            return;
        }

        $now = Carbon::now();
        $base = array_merge($this->data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if (isset($base['data']) && is_array($base['data'])) {
            $base['data'] = json_encode($base['data']);
        }

        foreach (array_chunk($userIds, self::CHUNK_SIZE) as $chunk) {
            $rows = [];
            foreach ($chunk as $userId) {
                $rows[] = array_merge($base, ['user_id' => $userId]);
            }
            Notification::insert($rows);
        }
    }
}
