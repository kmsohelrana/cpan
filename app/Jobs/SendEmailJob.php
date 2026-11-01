<?php

namespace App\Jobs;

use App\Helpers\LogHelper;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string|array $to,
        private string $subject,
        private string $body,
        private bool $isHtml = true,
        private string|array $cc = [],
        private string|array $bcc = [],
        private array $attachments = [],
    ) {}

    public function handle(): void
    {
        $recipients = is_array($this->to) ? implode(', ', $this->to) : $this->to;
        LogHelper::logEntry(LogHelper::LOG_INFO, 'Send Email to: ' . $recipients . ', Subject: ' . $this->subject);

        try {
            $sent = Mail::send([], [], function (Message $message) {
                $message->to($this->to)
                    ->subject($this->subject);

                if (!empty($this->cc)) {
                    $message->cc($this->cc);
                }

                if (!empty($this->bcc)) {
                    $message->bcc($this->bcc);
                }

                if ($this->isHtml) {
                    $message->html($this->body);
                } else {
                    $message->text($this->body);
                }

                foreach ($this->attachments as $attachment) {
                    if (isset($attachment['path'])) {
                        $message->attach($attachment['path'], $attachment['options'] ?? []);
                    }
                }
            });

            // Laravel reports "sent" once the message is handed to the transport; that does
            // NOT guarantee the mail server accepted/delivered it. Log the transport response
            // (message id + raw SMTP conversation) so undelivered mail can be investigated.
            $mailer    = config('mail.default');
            $messageId = null;
            $smtpDebug = null;

            if ($sent) {
                $symfonySent = $sent->getSymfonySentMessage();
                $messageId   = $symfonySent->getMessageId();
                $smtpDebug   = $symfonySent->getDebug(); // Full SMTP server conversation, incl. accept/reject codes
            }

            LogHelper::logEntry(
                LogHelper::LOG_INFO,
                'Email sent successfully. Mailer: ' . $mailer
                    . ', To: ' . $recipients
                    . ', MessageId: ' . ($messageId ?? 'N/A')
                    . ', SMTP Response: ' . ($smtpDebug ? trim($smtpDebug) : 'N/A')
            );
        } catch (Exception $e) {
            LogHelper::logEntry(LogHelper::LOG_ERROR, 'Email failed. To: ' . $recipients . ', Error: ' . $e->getMessage(), $e);
            throw $e;
        }
    }
}
