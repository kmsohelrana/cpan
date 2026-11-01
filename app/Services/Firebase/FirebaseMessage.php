<?php

namespace App\Services\Firebase;

use InvalidArgumentException;
use UnexpectedValueException;

class FirebaseMessage
{
    public const TYPE_TOPIC = 'topic';
    public const TYPE_DEVICE = 'device';

    private const ANDROID_PRIORITY_NORMAL = 'normal';
    private const ANDROID_PRIORITY_HIGH = 'high';

    private const APNS_PRIORITY_CONSERVE_POWER = '5';
    private const APNS_PRIORITY_IMMEDIATE = '10';

    private const WEB_PRIORITY_NORMAL = 'normal';
    private const WEB_PRIORITY_HIGH = 'high';

    private $device;
    private $topic;
    private $title;
    private $body;
    private $imageUrl;
    private $data;
    private $analyticsLabel;
    private $apnsConfig;
    private $androidConfig;
    private $webPushConfig;

    public function __construct($recipient, $type)
    {
        $this->processRecipient($recipient, $type);
    }

    public static function toDevice(string $device): self
    {
        return new self($device, self::TYPE_DEVICE);
    }

    public static function toTopic(string $topic): self
    {
        return new self($topic, self::TYPE_TOPIC);
    }

    private function processRecipient($recipient, $type)
    {
        if ($type == self::TYPE_TOPIC) {
            if (!preg_match('/^[a-zA-Z0-9-_.~%]{1,900}$/', $recipient)) {
                throw new InvalidArgumentException('Topic name format is invalid.');
            }
            $this->topic = $recipient;
        } else if ($type == self::TYPE_DEVICE) {
            $this->device = $recipient;
        } else {
            throw new InvalidArgumentException('Recipient type is invalid.');
        }
    }

    public function withTitle(string $title): self
    {
        $new = clone $this;
        $new->title = $title;

        return $new;
    }

    public function withBody(string $body): self
    {
        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    public function withSound(string $sound): self
    {
        $new = clone $this;
        //android
        $new->androidConfig['notification']['sound'] = $sound;
        //ios
        $new->apnsConfig['payload']['aps']['sound'] = $sound;

        return $new;
    }

    public function withChannelId($channelId): self
    {
        $new = clone $this;
        $new->androidConfig['notification']['channel_id'] = $channelId;
        return $new;
    }

    public function withImageUrl(string $imageUrl): self
    {
        $new = clone $this;
        //android
        $new->androidConfig['notification']['image'] = $imageUrl;
        //ios
        $new->apnsConfig['payload']['aps']['mutable-content'] = 1;
        $new->apnsConfig['fcm_options']['image'] = $imageUrl;
        //common
        $new->imageUrl = $imageUrl;

        return $new;
    }

    public function withData(array $data)
    {
        $validated = [];

        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new InvalidArgumentException('Data array keys must be a string.');
            }

            if (!($value === null || is_scalar($value))) {
                throw new InvalidArgumentException('Data array contains invalid values.');
            }

            $key = trim($key);

            if ($key == '') {
                throw new InvalidArgumentException('Data array keys must not be empty');
            }

            $tempKey = mb_strtolower($key);
            $reservedWords = ['from', 'message_type', 'notification'];
            $reservedPrefixes = ['gcm', 'google'];

            if (in_array($tempKey, $reservedWords, true)) {
                throw new InvalidArgumentException("'{$key}' can not be used as a key in data array.");
            }

            foreach ($reservedPrefixes as $prefix) {
                if (str_starts_with($tempKey, $prefix)) {
                    throw new InvalidArgumentException("Data array keys cannot have '{$prefix}' prefix.");
                }
            }

            $validated[$key] = $value;
        }

        $new = clone $this;
        $new->data = $validated;

        return $new;
    }

    public function withAnalyticsLabel(string $label): self
    {
        $new = clone $this;
        $new->analyticsLabel = $label;

        return $new;
    }

    public function withPriority($priority): self
    {
        if ($priority == 'high') {
            return $this->withHighPriority();
        } else {
            return $this->withNormalPriority();
        }
    }

    public function withNormalPriority(): self
    {
        $new = clone $this;
        $new->apnsConfig['headers']['apns-priority'] = self::APNS_PRIORITY_CONSERVE_POWER;
        $new->androidConfig['priority'] = self::ANDROID_PRIORITY_NORMAL;
        $new->webPushConfig['headers']['Urgency'] = self::WEB_PRIORITY_NORMAL;

        return $new;
    }

    public function withWebPushNotification(string $title, string $body): self
    {
        $new = clone $this;
        $new->webPushConfig['notification']['title'] = $title;
        $new->webPushConfig['notification']['body'] = $body;

        return $new;
    }

    public function withHighPriority(): self
    {
        $new = clone $this;
        $new->apnsConfig['headers']['apns-priority'] = self::APNS_PRIORITY_IMMEDIATE;
        $new->androidConfig['priority'] = self::ANDROID_PRIORITY_HIGH;
        $new->webPushConfig['headers']['Urgency'] = self::WEB_PRIORITY_HIGH;

        return $new;
    }

    public function toArray(): array
    {
        $payload = [];

        if ($this->device) {
            unset($payload['topic']);
            $payload['token'] = $this->device;
        } else if ($this->topic) {
            unset($payload['token']);
            $payload['topic'] = $this->topic;
        } else {
            throw new UnexpectedValueException('Message must have at least one recipient');
        }

        $notification = array_filter([
            'title' => $this->title,
            'body' => $this->body,
            'image' => $this->imageUrl,
        ], static function ($value) {
            return $value !== null;
        });

        $fcmOptions = array_filter([
            'analytics_label' => $this->analyticsLabel,
        ], static function ($value) {
            return $value !== null;
        });

        $payload = $payload + [
            'notification' => $notification,
            'data' => $this->data,
            'fcm_options' => $fcmOptions,
            'android' => $this->androidConfig,
            'apns' => $this->apnsConfig,
            'webpush' => $this->webPushConfig,
        ];

        if (empty($payload['notification']) && empty($payload['data'])) {
            throw new UnexpectedValueException('Message must have either notification or data or both');
        }

        return array_filter($payload, static function ($value) {
            return $value !== null && $value !== [];
        });
    }
}
