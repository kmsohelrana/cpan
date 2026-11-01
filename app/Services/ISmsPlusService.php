<?php

namespace App\Services;

use App\Helpers\LogHelper;
use Exception;
use Illuminate\Support\Facades\Http;

class ISmsPlusService
{
    private $baseUrl;
    private $token;
    private $sid;

    public function __construct()
    {
        $this->baseUrl = config('ismsplus.base_url');
        $this->token = config('ismsplus.token');
        $this->sid = config('ismsplus.sid');
    }

    public static function sendSMS($mobileNo, $messageBody, $masking = false): object
    {
        try {
            $instance = new self();
            if ($masking) $instance->sid = config('ismsplus.sid_masking');
            $action = '/api/v3/send-sms';
            $postData = $instance->generatePostBody($mobileNo, $messageBody);

            $response = Http::acceptJson()
                ->withOptions(['verify' => false])
                ->withHeaders($instance->headerData())
                ->post($instance->baseUrl . $action, $postData);

            $data['status'] = $response->status();
            $data['response'] = $response->collect();

            return (object)$data;
        } catch (Exception $e) {
            LogHelper::logEntry(LogHelper::LOG_EMERGENCY, $e->getMessage(), $e);
            return (object)['status' => 500, 'response' => null];
        }
    }

    public function generatePostBody($mobileNo, $messageBody)
    {
        return [
            "api_token" => $this->token,
            "sid" => $this->sid,
            "msisdn" => $mobileNo,
            "sms" => $messageBody,
            "csms_id" => time() . uniqid() . rand(1000, 9999)
        ];
    }

    protected function headerData(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }
}
