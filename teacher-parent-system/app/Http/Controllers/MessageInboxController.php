<?php

namespace App\Http\Controllers;

use App\Models\MessageRecipient;
use Illuminate\Http\Request;

class MessageInboxController extends Controller
{
    public function index(Request $request)
    {
        $recipients = MessageRecipient::with('message.sender')
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(15);

        if ($request->user()->isTeacher()) {
            $unreadCount = MessageRecipient::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count();

            return view('teacher.messages.inbox', compact('recipients', 'unreadCount'));
        }

        return view('messages.inbox', [
            'recipients' => $recipients,
            'layout' => 'layouts.parent',
            'portalLabel' => 'Student',
        ]);
    }

    public function show(Request $request, MessageRecipient $recipient)
    {
        abort_unless($recipient->user_id === $request->user()->id, 404);

        $recipient->load('message.sender');
        if ($recipient->read_at === null) {
            $recipient->update(['read_at' => now()]);
        }

        if ($request->user()->isTeacher()) {
            return view('teacher.messages.show', compact('recipient'));
        }

        return view('messages.show', [
            'recipient' => $recipient,
            'layout' => 'layouts.parent',
            'portalLabel' => 'Student',
        ]);
    }
}
