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

        return view('messages.inbox', [
            'recipients' => $recipients,
            'layout' => $request->user()->isTeacher() ? 'layouts.teacher' : 'layouts.parent',
            'portalLabel' => $request->user()->isTeacher() ? 'Teacher' : 'Student',
        ]);
    }

    public function show(Request $request, MessageRecipient $recipient)
    {
        abort_unless($recipient->user_id === $request->user()->id, 404);

        $recipient->load('message.sender');
        if ($recipient->read_at === null) {
            $recipient->update(['read_at' => now()]);
        }

        return view('messages.show', [
            'recipient' => $recipient,
            'layout' => $request->user()->isTeacher() ? 'layouts.teacher' : 'layouts.parent',
            'portalLabel' => $request->user()->isTeacher() ? 'Teacher' : 'Student',
        ]);
    }
}
