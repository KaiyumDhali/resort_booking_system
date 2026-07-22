<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    // ✅ Optimized — N+1 problem ফিক্স করা হয়েছে
    private function getChatUsersList()
    {
        $authId = auth()->id();

        // ✅ শুধুমাত্র auth user এর conversation গুলো নিয়ে আসি, users + last message সহ eager load
        $conversations = Conversation::whereHas('users', function ($q) use ($authId) {
                $q->where('user_id', $authId);
            })
            ->with([
                'users' => function ($q) use ($authId) {
                    $q->where('user_id', '!=', $authId);
                },
                'messages' => function ($q) {
                    $q->orderBy('id', 'desc')->limit(1);
                }
            ])
            ->get();

        $convMap = [];

        foreach ($conversations as $conv) {

            $otherUser = $conv->users->first();
            if (!$otherUser) continue;

            $lastMsg = $conv->messages->first();

            $unreadCount = Message::where('conversation_id', $conv->id)
                ->where('sender_id', $otherUser->id)
                ->whereNull('read_at')
                ->count();

            $unseenMessages = $unreadCount > 0
                ? Message::where('conversation_id', $conv->id)
                    ->where('sender_id', $otherUser->id)
                    ->whereNull('read_at')
                    ->orderBy('id', 'asc')
                    ->get()
                : collect();

            $convMap[$otherUser->id] = [
                'conversation_id'   => $conv->id,
                'last_message'      => $lastMsg->message ?? null,
                'last_message_time' => $lastMsg->created_at ?? null,
                'unread'            => $unreadCount,
                'unseen_messages'   => $unseenMessages,
            ];
        }

        $users = User::where('id', '!=', $authId)->get();

        foreach ($users as $user) {
            $data = $convMap[$user->id] ?? null;

            $user->last_message      = $data['last_message'] ?? null;
            $user->last_message_time = $data['last_message_time'] ?? null;
            $user->unread            = $data['unread'] ?? 0;
            $user->unseen_messages   = $data['unseen_messages'] ?? collect();
            $user->has_new           = $user->unread > 0;
        }

        // ✅ যার সাম্প্রতিক message আছে সে সবার উপরে, message নেই যাদের তারা নিচে
        return $users->sortByDesc(function ($user) {
            return $user->last_message_time ? $user->last_message_time->timestamp : 0;
        })->values();
    }

    // user list (blade view)
    public function users()
    {
        $users = $this->getChatUsersList();

        return view('chat.users', compact('users'));
    }

    // open chat
    public function openChat($userId)
    {
        $authId = auth()->id();

        $conversation = Conversation::whereHas('users', function ($q) use ($authId) {
                $q->where('user_id', $authId);
            })
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$authId, $userId]);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $authId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        $messages = Message::with('sender')
            ->where('conversation_id', $conversation->id)
            ->orderBy('id', 'asc')
            ->get();

        $user = User::find($userId);

        return view('chat.box', compact('conversation', 'messages', 'user'));
    }

    // send message
    public function sendMessage(Request $request)
    {
        $msg = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'read_at' => null,
        ]);

        return response()->json($msg);
    }

    // load messages (AJAX)
    public function loadMessages($conversationId)
    {
        return Message::with('sender')
            ->where('conversation_id', $conversationId)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function markSeen($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return response()->json(['status' => true]);
    }

    public function chatNotifications()
    {
        $authId = auth()->id();

        $notifications = [];

        $conversations = Conversation::whereHas('users', function ($q) use ($authId) {
            $q->where('user_id', $authId);
        })->get();

        foreach ($conversations as $conversation) {

            $unread = Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $authId)
                ->whereNull('read_at')
                ->count();

            if ($unread > 0) {

                $lastMessage = Message::where('conversation_id', $conversation->id)
                    ->latest('id')
                    ->first();

                $sender = User::find($lastMessage->sender_id);

                $notifications[] = [
                    'conversation_id' => $conversation->id,
                    'sender_name'     => $sender?->name,
                    'message'         => $lastMessage?->message,
                    'unread'          => $unread,
                    'user_id'         => $sender?->id,
                    'time'            => $lastMessage?->created_at?->diffForHumans(),
                ];
            }
        }

        return response()->json($notifications);
    }

    public function typing(Request $request)
    {
        $key = "typing_{$request->conversation_id}_{$request->user_id}";

        Cache::put($key, true, now()->addSeconds(3));

        return response()->json(['status' => true]);
    }

    public function typingStatus($conversationId, $userId)
    {
        $key = "typing_{$conversationId}_{$userId}";

        return response()->json([
            'typing' => Cache::has($key)
        ]);
    }

    // JSON version for AJAX auto-refresh list
    public function usersListJson()
    {
        $users = $this->getChatUsersList();

        $data = $users->map(function ($user) {
            return [
                'id'                => $user->id,
                'name'              => $user->name,
                'avatar_letter'     => strtoupper(substr($user->name, 0, 1)),
                'last_message'      => $user->last_message ?? 'No messages yet',
                'last_message_time' => $user->last_message_time ? $user->last_message_time->timestamp : 0,
                'unread'            => $user->unread,
                'has_new'           => $user->has_new,
                'unseen_messages'   => $user->unseen_messages
                    ? $user->unseen_messages->map(fn($m) => ['message' => $m->message])
                    : [],
            ];
        });

        return response()->json($data->values());
    }
}