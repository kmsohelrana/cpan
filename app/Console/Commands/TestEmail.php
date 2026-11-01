<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Console\Command;

class TestEmail extends Command
{
    protected $signature = 'email:test';
    protected $description = 'Send a test email to a given address (requires Super Admin login)';

    public function handle(): void
    {
        if (!$this->authenticateSuperAdmin()) {
            $this->error('Authentication failed. Only a Super Admin can run this command.');
            return;
        }

        if (!isEmailConfigured()) {
            $this->error('Email is not configured.');
            return;
        }

        $email = $this->ask('Enter recipient email address');

        if (empty($email)) {
            $this->error('Email address is required.');
            return;
        }

        $subject = $this->ask('Enter email subject', 'Test email from CRM panel');
        $body = $this->ask('Enter email body', 'This is a test email sent from the CRM panel.');

        $this->info('Dispatching email...');

        SendEmailJob::dispatch($email, $subject, $body);

        $this->info('Job dispatched successfully.');
    }

    private function authenticateSuperAdmin(): bool
    {
        $email = $this->ask('Enter Super Admin email');
        $password = $this->secret('Enter Super Admin password');

        $user = User::query()->where('email', $email)->first();

        if (!$user || !$user->hasRole(RoleEnum::SUPER_ADMIN->value) || !verifyPassword($password, $user->password)) {
            return false;
        }

        return true;
    }
}
