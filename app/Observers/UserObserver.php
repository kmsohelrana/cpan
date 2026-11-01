<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserAudit;

class UserObserver
{
    public function created(User $user): void
    {
        $this->syncAudit($user, 'created');
    }

    public function updated(User $user): void
    {
        $this->syncAudit($user, 'updated');
    }

    public function deleted(User $user): void
    {
        $this->syncAudit($user, 'deleted');
    }

    private function syncAudit(User $user, string $action): void
    {
        if ($action !== 'deleted') {
            $user = $user->fresh();
        }

        if (!$user) {
            return;
        }

        UserAudit::create([
            'master_id'   => $user->id,
            'action'      => $action,
            'action_by'   => authUser()?->id,
            'employee_id' => $user->employee_id,
            'name'        => $user->name,
            'mobile'      => $user->mobile,
            'email'       => $user->email,
            'image'       => $user->getRawOriginal('image'),
            'language'    => $user->language,
            'status'      => $user->getRawOriginal('status'),
        ]);
    }
}
