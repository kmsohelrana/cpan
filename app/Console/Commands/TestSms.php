<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Jobs\SendSmsJob;
use App\Models\User;
use Illuminate\Console\Command;

class TestSms extends Command
{
    protected $signature = 'sms:test';
    protected $description = 'Send a test SMS to a given mobile number (requires Super Admin login)';

    public function handle(): void
    {
        if (!$this->authenticateSuperAdmin()) {
            $this->error('Authentication failed. Only a Super Admin can run this command.');
            return;
        }

        if (!isSmsConfigured()) {
            $this->error('SMS gateway is not configured.');
            return;
        }

        $mobile = $this->ask('Enter mobile number to send the test SMS to');

        if (empty($mobile)) {
            $this->error('Mobile number is required.');
            return;
        }

        $message = $this->ask('Enter test message', 'This is a test SMS from the CRM panel.');

        $this->info('Dispatching SMS...');

        SendSmsJob::dispatch($mobile, $message);

        $this->info('Job dispatched successfully.');
    }

    /**
     * Require valid Super Admin credentials before allowing this command to run,
     * since it sends a real SMS through the paid gateway.
     */
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
