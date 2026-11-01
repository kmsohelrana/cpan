<?php

namespace App\Http\Controllers;

use App\Enums\CommonEnum;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Notifications';
        $data = $this->notificationList($request);
        return view('notifications.index', compact('title', 'data'));
    }

    public function notificationList(Request $request)
    {
        $title = 'Notifications';
        $notifications = Notification::query()
            ->where('user_id', Auth::id())
            ->where('model_type', User::class)
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            }))
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'read') {
                    $q->whereNotNull('read_at');
                } elseif ($request->status === 'unread') {
                    $q->whereNull('read_at');
                }
            })
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByRaw('read_at IS NULL DESC')
            ->latest()
            ->paginate(CommonEnum::PER_PAGE->value());

        return view('notifications.table', compact('title', 'notifications'));
    }

    public function markRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllRead(): JsonResponse
    {
        Notification::query()
            ->where('user_id', Auth::id())
            ->where('model_type', User::class)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }
}
