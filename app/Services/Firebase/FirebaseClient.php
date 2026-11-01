<?php

namespace App\Services\Firebase;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FirebaseClient
{
    private $accessToken;
    private $projectId;
    private $guzzleClient;
    private string $app;

    public function __construct(string $app = 'merchant')
    {
        $this->app = $app;

        $serviceAccount = match($app) {
            'crm'   => storage_path('app/firebase/firebase-service-account-crm.json'),
            default => storage_path('app/firebase/firebase-service-account-merchant.json'),
        };

        if (!file_exists($serviceAccount)) {
            throw new \Exception('Firebase service account file not found.');
        }

        $serviceAccountData = file_get_contents($serviceAccount);

        $this->generateAccessToken($serviceAccountData);

        $this->guzzleClient = new GuzzleClient([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function validateFcmTokens(array $fcmTokens): object
    {
        $response = $this->sendMulticast([], $fcmTokens, true);

        return $response;
    }

    public function send($message, bool $validateOnly = false): object
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $response = Http::asJson()
            ->withToken($this->accessToken)
            ->post($url, ['message' => $message->toArray(), 'validate_only' => $validateOnly]);

        $data['status'] = $response->status();
        $data['response'] = $response->collect();

        return (object)$data;
    }

    public function sendMulticast($message, $fcmTokens, bool $validateOnly = false): object
    {
        $messages = [];
        $messageArray = $message->toArray();
        unset($messageArray['topic'], $messageArray['condition']);
        foreach ($fcmTokens as $fcmToken) {
            $msgCopy = $messageArray;
            $msgCopy['token'] = $fcmToken;
            $messages[] = $msgCopy;
        }
        return $this->sendAll($messages, $validateOnly);
    }

    public function sendAll($messages, bool $validateOnly = false): object
    {
        $promises = $this->createMessageRequestPromises($messages, $validateOnly);

        $data = Utils::settle($promises())
            ->then(static function (array $responses) use ($messages, $validateOnly) {
                $reports = ['status' => 200, 'response' => null, 'valid_fcm_tokens' => null, 'invalid_fcm_tokens' => null];
                foreach ($responses as $index => $response) {
                    if ($response['state'] === PromiseInterface::FULFILLED) {
                        $reports['response'][] = [
                            'status' => $response['value']->getStatusCode(),
                            'response' => json_decode($response['value']->getBody()),
                        ];
                        if ($validateOnly && isset($messages[$index]['token'])) {
                            $reports['valid_fcm_tokens'][] = $messages[$index]['token'];
                        }
                    } elseif ($response['state'] === PromiseInterface::REJECTED) {
                        if ($response['reason'] instanceof RequestException) {
                            $responseData = $response['reason']->getResponse();
                        } else {
                            $responseData = null;
                        }
                        $reports['response'][] = [
                            'status' => $responseData ? $responseData->getStatusCode() : 500,
                            'response' => $responseData ? json_decode($responseData->getBody()) : json_decode($response['reason']),
                        ];
                        if ($validateOnly && isset($messages[$index]['token'])) {
                            $reports['invalid_fcm_tokens'][] = $messages[$index]['token'];
                        }
                    }
                }
                return $reports;
            })
            ->wait();

        return (object)$data;
    }

    private function createMessageRequestPromises($messages, bool $validateOnly): callable
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        return function () use ($messages, $validateOnly, $url) {
            foreach ($messages as $message) {
                yield $this->guzzleClient->requestAsync('POST', $url, [
                    'json' => ['message' => $message, 'validate_only' => $validateOnly],
                ]);
            }
        };
    }

    // Helpers
    protected function generateAccessToken($serviceAccountData)
    {
        try {
            $cacheKey = 'firebase_token_' . $this->app;
            if (Cache::has($cacheKey)) {
                $this->accessToken = Cache::get($cacheKey);
                $this->projectId = json_decode($serviceAccountData)->project_id;
            } else {
                $authConfig = json_decode($serviceAccountData);

                $secret = openssl_get_privatekey($authConfig->private_key);

                $header = json_encode([
                    'typ' => 'JWT',
                    'alg' => 'RS256'
                ]);

                $timestamp = time();

                $payload = json_encode([
                    "iss" => $authConfig->client_email,
                    "scope" => "https://www.googleapis.com/auth/firebase.messaging",
                    "aud" => $authConfig->token_uri,
                    "exp" => $timestamp + 3600,
                    "iat" => $timestamp
                ]);

                $base64UrlHeader = self::base64UrlEncode($header);
                $base64UrlPayload = self::base64UrlEncode($payload);

                $result = openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $secret, OPENSSL_ALGO_SHA256);

                $base64UrlSignature = self::base64UrlEncode($signature);

                $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

                $postBody = [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ];

                $response = Http::acceptJson()->post($authConfig->token_uri, $postBody);

                if ($response->status() == 200) {
                    Cache::put($cacheKey, $response->collect()->get('access_token'), now()->addMinutes(58));

                    $this->accessToken = $response->collect()->get('access_token');
                    $this->projectId = $authConfig->project_id;
                }
            }
        } catch (Exception $e) {
            $this->accessToken = null;
            $this->projectId = null;
            throw new Exception('Failed to generate Firebase access token: ' . $e->getMessage());
        }
    }

    protected function base64UrlEncode(string $data): string
    {
        $base64Url = strtr(base64_encode($data), '+/', '-_');

        return rtrim($base64Url, '=');
    }

    protected function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }
}
