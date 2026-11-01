<?php

namespace App\Services\HealthMonitoring;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActiveSessionsService
{
    /**
     * Own page param: the dashboard also paginates Recent Events on the default
     * `page`, so sharing it would move both tables at once.
     */
    public const PAGE_NAME = 'sessions_page';

    /**
     * Currently logged-in web (CRM/admin panel) users, read live from the `sessions`
     * table — not cached, since staleness here would be actively misleading. Only
     * authenticated sessions still within the configured session lifetime are
     * considered "active"; older rows are expired but not yet garbage-collected.
     */
    public function getActiveSessions(int $perPage = 20): LengthAwarePaginator
    {
        $expiresAfter = Carbon::now()->subMinutes(config('session.lifetime'))->getTimestamp();
        $currentSessionId = session()->getId();

        return DB::table('sessions')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>=', $expiresAfter)
            ->orderByDesc('sessions.last_activity')
            ->paginate(
                $perPage,
                ['sessions.id', 'sessions.ip_address', 'sessions.user_agent', 'sessions.last_activity', 'users.name', 'users.email'],
                self::PAGE_NAME
            )
            // Keep any other section's query params (e.g. Recent Events' page) alive,
            // and jump back to this table rather than the top of a long dashboard.
            ->withQueryString()
            ->fragment('section-active-sessions')
            ->through(fn ($row) => [
                'is_current' => $row->id === $currentSessionId,
                'name' => $row->name,
                'email' => $row->email,
                'ip_address' => $row->ip_address ?? 'Unknown',
                'user_agent' => $row->user_agent ?? 'Unknown',
                'last_active' => Carbon::createFromTimestamp($row->last_activity)->diffForHumans(),
                'last_active_at' => Carbon::createFromTimestamp($row->last_activity)->toDateTimeString(),
            ]);
    }
}
