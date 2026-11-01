<?php

namespace App\Jobs;

use App\Enums\NotificationStatusEnum;
use App\Helpers\LogHelper;
use App\Models\Notification;
use App\Services\Firebase\FirebaseClient;
use App\Services\Firebase\FirebaseMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fcmTokens;
    private $title;
    private $body;
    private $userId;
    private $type;
    private $additionalData;
    private ?string $image;
    private ?string $sound;
    private $storeInDatabase;
    private string $firebaseApp;

    /**
     * Create a robust, multi-purpose job instance.
     */
    public function __construct(
        $fcmTokens,
        $title,
        $body,
        $userId = null,
        $type = null,
        $additionalData = [],
        ?string $image = null,
        ?string $sound = null,
        $storeInDatabase = true,
        string $firebaseApp = 'merchant',
    ) {
        $this->fcmTokens = $fcmTokens;
        $this->title = $title;
        $this->body = $body;
        $this->userId = $userId;
        $this->type = $type;
        $this->additionalData = $additionalData;
        $this->image = $image;
        $this->sound = $sound;
        $this->storeInDatabase = $storeInDatabase;
        $this->firebaseApp = $firebaseApp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $isBulk = is_array($this->fcmTokens);

        LogHelper::logEntry(LogHelper::LOG_INFO, 'Processing Push Request. Firebase App: ' . $this->firebaseApp . ' Bulk: ' . ($isBulk ? 'Yes' : 'No') . ', Title: ' . $this->title);

        $notification = $this->storeNotification(NotificationStatusEnum::PENDING);

        $recipient = $isBulk ? 'multicast' : $this->fcmTokens;
        $data = ['title' => $this->title, 'body' => $this->body];
        if (!empty($this->additionalData)) {
            $data = array_merge($data, $this->additionalData);
        }
        $message = FirebaseMessage::toDevice($recipient)
            // ->withTitle($this->title)
            // ->withBody($this->body)
            ->withData($data)
            ->withWebPushNotification($this->title, $this->body)
            // ->withChannelId(config('app.name', 'Acquiring Platform'))
            ->withHighPriority();

        if ($this->sound) {
            $message = $message->withSound($this->sound);
        }

        if ($this->image) {
            $message = $message->withImageUrl($this->image);
        }

        $client = new FirebaseClient($this->firebaseApp);

        if ($isBulk) {
            $response = $client->sendMulticast($message, $this->fcmTokens);
        } else {
            $response = $client->send($message);
        }

        if ($response->status == 200) {
            LogHelper::logEntry(LogHelper::LOG_INFO, 'Push Notification processed cleanly.');
            if ($notification) {
                $notification->update(['status' => NotificationStatusEnum::SENT->value]);
            }
        } else {
            LogHelper::logEntry(LogHelper::LOG_ERROR, 'Push Notification failed. Response: ' . json_encode($response->response));
            if ($notification) {
                $notification->update(['status' => NotificationStatusEnum::FAILED->value]);
            }
        }
        LogHelper::logEntry(LogHelper::LOG_INFO, 'Push Notification processing completed.');
    }

    /**
     * Store the notification in the database if required.
     *
     * @param NotificationStatusEnum $status
     * @return Notification|null
     */
    private function storeNotification(NotificationStatusEnum $status): ?Notification
    {
        if (!$this->storeInDatabase || !$this->userId || !$this->type) {
            return null;
        }

        // $message = str_replace(["\r\n", "\n", "\r"], " ", $this->body);

        return Notification::create([
            'user_id' => $this->userId,
            'title' => $this->title,
            'message' => $this->body,
            'model_type' => $this->type,
            'status' => $status->value,
        ]);
    }
}
