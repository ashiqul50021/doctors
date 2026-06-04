<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
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
            
            // Check booking constraint
            if ($activeContact) {
                $allowed = true;
                if ($user->role === 'patient' && $activeContact->role === 'doctor') {
                    $patient = $user->patient;
                    $doctor = $activeContact->doctor;
                    if (!$patient || !$doctor || !Appointment::where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->exists()) {
                        $allowed = false;
                    }
                } elseif ($user->role === 'doctor' && $activeContact->role === 'patient') {
                    $doctor = $user->doctor;
                    $patient = $activeContact->patient;
                    if (!$doctor || !$patient || !Appointment::where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->exists()) {
                        $allowed = false;
                    }
                }

                if (!$allowed) {
                    $redirectRoute = ($user->role === 'doctor') ? 'doctors.dashboard' : 'patient.dashboard';
                    if (url()->previous() && url()->previous() !== url()->current()) {
                        return redirect()->back()->with('error', 'এই ডাক্তারের সাথে চ্যাট করতে প্রথমে একটি অ্যাপয়েন্টমেন্ট বুক করুন।');
                    }
                    return redirect()->route($redirectRoute)->with('error', 'এই ডাক্তারের সাথে চ্যাট করতে প্রথমে একটি অ্যাপয়েন্টমেন্ট বুক করুন।');
                }
            }

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

        // Mark incoming messages from active contact as read
        if ($activeContact) {
            Message::where('sender_id', $activeContact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
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

        $user = Auth::user();
        $receiver = User::find($request->receiver_id);

        if ($receiver) {
            $allowed = true;
            if ($user->role === 'patient' && $receiver->role === 'doctor') {
                $patient = $user->patient;
                $doctor = $receiver->doctor;
                if (!$patient || !$doctor || !Appointment::where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->exists()) {
                    $allowed = false;
                }
            } elseif ($user->role === 'doctor' && $receiver->role === 'patient') {
                $doctor = $user->doctor;
                $patient = $receiver->patient;
                if (!$doctor || !$patient || !Appointment::where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->exists()) {
                    $allowed = false;
                }
            }

            if (!$allowed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'এই ডাক্তারের সাথে চ্যাট করতে প্রথমে একটি অ্যাপয়েন্টমেন্ট বুক করুন।',
                ], 403);
            }
        }

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
