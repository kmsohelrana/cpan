<?php

namespace App\Console\Commands;

use App\Jobs\SendPushNotificationJob;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    protected $signature = 'push-notification:test';
    protected $description = 'Send a test push notification to a given FCM token';

    public function handle(): void
    {
        $token = $this->ask('Enter FCM token');

        if (empty($token)) {
            $this->error('Token is required.');
            return;
        }

        $this->info('Dispatching push notification...');

        SendPushNotificationJob::dispatch(
            $token,
            'Test Push Notification',
            'This is a test push from the CRM panel.',
            storeInDatabase: false,
        );

        $this->info('Job dispatched successfully.');
    }
}
