@extends('layouts.teacher')
@section('title', 'Edit '.$homework->title)

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('teacher.homework.show', $homework) }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to {{ $homework->title }}
    </a>

    <h1 class="mt-4 text-2xl font-bold text-gray-800">Edit timeline</h1>
    <p class="mt-1 text-sm text-gray-500">
        Extending the due date or duration only ever gives students more time — it never takes time away from
        an attempt already in progress.
    </p>

    <form method="POST" action="{{ route('teacher.homework.update', $homework) }}" class="ui-card mt-6 space-y-4 p-6">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="ui-field-label">Title</label>
            <input id="title" name="title" value="{{ old('title', $homework->title) }}" maxlength="255" required class="ui-input">
            @error('title')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="instructions" class="ui-field-label">Instructions</label>
            <textarea id="instructions" name="instructions" rows="3" class="ui-input resize-none">{{ old('instructions', $homework->instructions) }}</textarea>
            @error('instructions')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="due_at" class="ui-field-label">Due date/time</label>
                <input type="datetime-local" id="due_at" name="due_at"
                       value="{{ old('due_at', optional($homework->due_at)->format('Y-m-d\TH:i')) }}" class="ui-input">
                @error('due_at')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            @if($homework->isQuiz())
                <div>
                    <label for="duration_minutes" class="ui-field-label">Duration once started (minutes)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1" max="600"
                           value="{{ old('duration_minutes', $homework->duration_minutes) }}" class="ui-input">
                    @error('duration_minutes')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
            @endif
        </div>

        <button type="submit" class="ui-submit-btn" style="width:100%;justify-content:center">Save changes</button>
    </form>
</div>
@endsection
