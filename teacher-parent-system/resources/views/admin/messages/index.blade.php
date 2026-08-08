@extends('layouts.admin')
@section('title', 'Messages')

@section('content')
<div class="max-w-6xl">
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-gray-800">Messages</h1>
        <p class="mt-1 text-sm text-gray-500">Send separate notices to teachers or students, school-wide or for selected classes.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-800">Send a message</h2>
            <form method="POST" action="{{ route('admin.messages.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-700">Recipients</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="recipient-option cursor-pointer rounded-lg border p-3 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="audience" value="teacher" class="sr-only" @checked(old('audience', 'teacher') === 'teacher')>
                            <span class="block text-sm font-semibold text-gray-800">Teachers</span>
                            <span class="mt-0.5 block text-xs text-gray-500">Teacher portal inbox</span>
                        </label>
                        <label class="recipient-option cursor-pointer rounded-lg border p-3 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="audience" value="parent" class="sr-only" @checked(old('audience') === 'parent')>
                            <span class="block text-sm font-semibold text-gray-800">Students</span>
                            <span class="mt-0.5 block text-xs text-gray-500">Student portal inbox</span>
                        </label>
                    </div>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-700">Delivery area</p>
                    <div class="flex gap-4 text-sm text-gray-700">
                        <label class="cursor-pointer"><input type="radio" name="delivery_scope" value="all" class="mr-1.5 text-indigo-600" @checked(old('delivery_scope', 'all') === 'all')> All classes</label>
                        <label class="cursor-pointer"><input id="classes-scope" type="radio" name="delivery_scope" value="classes" class="mr-1.5 text-indigo-600" @checked(old('delivery_scope') === 'classes')> Selected classes</label>
                    </div>
                    <div id="class-picker" class="mt-3 grid max-h-40 grid-cols-2 gap-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3">
                        @forelse($classes as $class)
                            <label class="flex cursor-pointer items-center gap-2 rounded px-1 py-1 text-sm text-gray-700 hover:bg-white">
                                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($class->id, old('class_ids', [])))>
                                {{ $class->name }}
                            </label>
                        @empty
                            <p class="col-span-2 text-xs text-gray-500">No classes have been created yet.</p>
                        @endforelse
                    </div>
                    @error('class_ids')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="subject" class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                    <input id="subject" name="subject" value="{{ old('subject') }}" maxlength="255" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="body" class="mb-1 block text-sm font-medium text-gray-700">Message</label>
                    <textarea id="body" name="body" rows="7" maxlength="5000" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('body') }}</textarea>
                    @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Send message</button>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-base font-bold text-gray-800">Sent messages</h2>
            </div>
            @forelse($messages as $message)
                <article class="border-b border-gray-100 px-6 py-4 last:border-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-gray-800">{{ $message->subject }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $message->body }}</p>
                            <p class="mt-2 text-xs text-gray-400">To {{ $message->audience === 'teacher' ? 'teachers' : 'students' }}{{ $message->target_class_ids ? ' in selected classes' : ' in all classes' }} · {{ $message->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <a href="{{ route('admin.messages.show', $message) }}" class="shrink-0 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">{{ $message->read_count }}/{{ $message->recipients_count }} read</a>
                    </div>
                </article>
            @empty
                <p class="px-6 py-10 text-center text-sm text-gray-500">No messages have been sent yet.</p>
            @endforelse
            @if($messages->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $messages->links() }}</div>@endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    #class-picker.is-disabled { opacity: .45; pointer-events: none; }
</style>
@endpush

@push('scripts')
<script>
    const classScope = document.getElementById('classes-scope');
    const classPicker = document.getElementById('class-picker');
    const updateClassPicker = () => classPicker.classList.toggle('is-disabled', !classScope.checked);
    document.querySelectorAll('input[name="delivery_scope"]').forEach((input) => input.addEventListener('change', updateClassPicker));
    updateClassPicker();
</script>
@endpush
