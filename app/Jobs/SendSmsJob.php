<?php

namespace App\Jobs;

use App\Helpers\LogHelper;
use App\Services\ISmsPlusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $mobile,
        private string $message,
        private bool $masking = false,
    ) {}

    public function handle(): void
    {
        LogHelper::logEntry(LogHelper::LOG_INFO, 'Send SMS to: ' . $this->mobile);

        $response = ISmsPlusService::sendSMS($this->mobile, $this->message, $this->masking);
 
        if ($response->status !== 200 || (is_object($response->response) && $response->response->get('status_code') !== 200)) {
            LogHelper::logEntry(LogHelper::LOG_ERROR, 'SMS failed. Mobile: ' . $this->mobile . ', Response: ' . json_encode($response->response));
        } else {
            LogHelper::logEntry(LogHelper::LOG_INFO, 'SMS sent response. Mobile: ' . $this->mobile . ', Response: ' . json_encode($response->response));
        }
    }
}
