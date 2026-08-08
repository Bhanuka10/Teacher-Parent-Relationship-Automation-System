<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolMessage;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $messages = SchoolMessage::with('sender')
            ->withCount([
                'recipients as recipients_count',
                'recipients as read_count' => fn ($query) => $query->whereNotNull('read_at'),
            ])
            ->latest()
            ->paginate(12);

        $classes = SchoolClass::orderBy('name')->get(['id', 'name']);

        return view('admin.messages.index', compact('messages', 'classes'));
    }

    public function show(SchoolMessage $message)
    {
        $message->load([
            'recipients.user.schoolClass',
            'recipients.user.students.schoolClass',
        ]);

        return view('admin.messages.show', compact('message'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audience' => ['required', 'in:teacher,parent'],
            'delivery_scope' => ['required', 'in:all,classes'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['integer', 'exists:school_classes,id'],
            'subject'  => ['required', 'string', 'max:255'],
            'body'     => ['required', 'string', 'max:5000'],
        ]);

        if ($validated['delivery_scope'] === 'classes' && empty($validated['class_ids'])) {
            return back()->withInput()->withErrors(['class_ids' => 'Select at least one class.']);
        }

        $classIds = $validated['delivery_scope'] === 'classes' ? $validated['class_ids'] : null;
        $recipients = User::where('role', $validated['audience']);

        if ($classIds) {
            if ($validated['audience'] === 'teacher') {
                $recipients->whereHas('schoolClass', fn ($query) => $query->whereIn('id', $classIds));
            } else {
                $recipients->whereHas('students', fn ($query) => $query->whereIn('school_class_id', $classIds));
            }
        }

        $recipientIds = $recipients->pluck('id');

        if ($recipientIds->isEmpty()) {
            return back()->withInput()->with('error', 'There are no '.($validated['audience'] === 'teacher' ? 'teachers' : 'students').' to receive this message.');
        }

        DB::transaction(function () use ($validated, $recipientIds, $classIds) {
            $message = SchoolMessage::create([
                'sender_id' => auth()->id(),
                'audience' => $validated['audience'],
                'target_class_ids' => $classIds,
                'subject' => $validated['subject'],
                'body' => $validated['body'],
            ]);

            $message->recipients()->createMany(
                $recipientIds->map(fn ($id) => ['user_id' => $id])->all()
            );
        });

        $audience = $validated['audience'] === 'teacher' ? 'teachers' : 'students';
        $target = $classIds ? ' in the selected classes' : '';

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message sent to '.$recipientIds->count().' '.$audience.$target.'.');
    }
}
