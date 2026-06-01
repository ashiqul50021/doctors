<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get recent chat users
        $sentMessageUserIds = Message::where('sender_id', $user->id)->pluck('receiver_id')->toArray();
        $receivedMessageUserIds = Message::where('receiver_id', $user->id)->pluck('sender_id')->toArray();
        $userIds = array_unique(array_merge($sentMessageUserIds, $receivedMessageUserIds));

        // Let's get contacts. If no contacts, maybe suggest some doctors/patients?
        // For simplicity, let's just get the users from the IDs.
        $contacts = User::whereIn('id', $userIds)->get();

        // If the user wants to chat with a new person not in history
        $activeContactId = $request->get('user_id');
        $activeContact = null;
        $messages = collect();

        if ($activeContactId) {
            $activeContact = User::find($activeContactId);
            if ($activeContact && !$contacts->contains('id', $activeContact->id)) {
                $contacts->push($activeContact);
            }
            if ($activeContact) {
                $messages = Message::where(function ($q) use ($user, $activeContactId) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $activeContactId);
                    })
                    ->orWhere(function ($q) use ($user, $activeContactId) {
                        $q->where('sender_id', $activeContactId)->where('receiver_id', $user->id);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        } elseif ($contacts->count() > 0) {
            // Select the first contact by default
            $activeContact = $contacts->first();
            $activeContactId = $activeContact->id;
            $messages = Message::where(function ($q) use ($user, $activeContactId) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $activeContactId);
                })
                ->orWhere(function ($q) use ($user, $activeContactId) {
                    $q->where('sender_id', $activeContactId)->where('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $view = ($user->role === 'doctor') ? 'frontend.chat-doctor' : 'frontend.chat';
        if (!view()->exists($view)) {
            $view = 'frontend.chat';
        }

        return view($view, compact('contacts', 'activeContact', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success', 
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'time' => $message->created_at->format('g:i A'),
            ]
        ]);
    }
}
