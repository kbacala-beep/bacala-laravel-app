<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | inbox — list of unique conversations for the current user
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $userId = Auth::id();

        // Get the latest message for each unique conversation partner
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($userId) {
                // Group by the other person's ID
                return $message->sender_id === $userId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function ($messages) use ($userId) {
                $latest = $messages->first(); // already sorted latest first
                $partner = $latest->sender_id === $userId
                    ? $latest->receiver
                    : $latest->sender;

                $unreadCount = $messages->filter(function ($m) use ($userId) {
                    return $m->receiver_id === $userId && $m->read_at === null;
                })->count();

                return [
                    'partner' => $partner,
                    'latest' => $latest,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(fn($c) => $c['latest']->created_at)
            ->values();

        // Users available to start a new conversation with
        $users = User::where('id', '!=', $userId)->orderBy('name')->get(['id', 'name', 'profile_photo']);

        return view('messages.index', compact('conversations', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | conversation — chat view between current user and another user
    |--------------------------------------------------------------------------
    */
    public function conversation(User $user)
    {
        $myId = Auth::id();

        if ($user->id === $myId) {
            return redirect()->route('messages.inbox')
                ->with('error', 'You cannot message yourself.');
        }

        // Load all messages between the two users
        $messages = Message::where(function ($q) use ($myId, $user) {
            $q->where('sender_id', $myId)->where('receiver_id', $user->id);
        })
            ->orWhere(function ($q) use ($myId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $myId);
            })
            ->with(['sender', 'receiver'])
            ->oldest()
            ->get();

        // Mark all unread messages from this partner as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('messages', 'user'));
    }

    /*
    |--------------------------------------------------------------------------
    | send — AJAX only
    |--------------------------------------------------------------------------
    */
    public function send(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'You cannot message yourself.'], 422);
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $user->id,
                'body' => $request->body,
            ]);

            $message->load('sender');

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'sender_photo' => $message->sender->profile_photo
                        ? asset('storage/' . $message->sender->profile_photo)
                        : asset('img/default-user.png'),
                    'created_at' => $message->created_at->format('M d, Y H:i'),
                    'time_ago' => $message->created_at->diffForHumans(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('MessageController@send failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Failed to send message.'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | poll — returns new messages since a given ID (AJAX polling)
    |--------------------------------------------------------------------------
    */
    public function poll(Request $request, User $user)
    {
        $myId = Auth::id();
        $sinceId = (int) $request->input('since_id', 0);

        $messages = Message::where('sender_id', $user->id)
            ->where('receiver_id', $myId)
            ->where('id', '>', $sinceId)
            ->oldest()
            ->get();

        // Mark them as read immediately
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_id' => $m->sender_id,
                'sender_name' => $user->name,
                'sender_photo' => $user->profile_photo
                    ? asset('storage/' . $user->profile_photo)
                    : asset('img/default-user.png'),
                'created_at' => $m->created_at->format('M d, Y H:i'),
                'time_ago' => $m->created_at->diffForHumans(),
            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | unreadCount — for navbar badge polling (AJAX)
    |--------------------------------------------------------------------------
    */
    public function unreadCount()
    {
        try {
            $myId = Auth::id();

            // 1. Get total unread count
            $count = Message::where('receiver_id', $myId)
                ->whereNull('read_at')
                ->count();

            // 2. Get the latest message from each unread sender
            $recent = Message::where('receiver_id', $myId)
                ->whereNull('read_at')
                ->with('sender')
                ->latest()
                ->get()
                // Group by sender so we don't show the same person 3 times
                ->unique('sender_id')
                ->take(5)
                ->map(function ($m) {
                    if (!$m->sender)
                        return null;

                    return [
                        'sender_name' => $m->sender->name,
                        'sender_photo' => $m->sender->profile_photo
                            ? asset('storage/' . $m->sender->profile_photo)
                            : asset('img/default-user.png'),
                        'preview' => \Str::limit($m->body, 40),
                        'time_ago' => $m->created_at->diffForHumans(),
                        // Ensure this route name matches your web.php
                        'url' => route('messages.conversation', $m->sender_id),
                    ];
                })
                ->filter()
                ->values();

            return response()->json([
                'count' => $count,
                'recent' => $recent,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
