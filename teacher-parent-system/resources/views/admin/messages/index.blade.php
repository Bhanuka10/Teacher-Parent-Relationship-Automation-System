@extends('layouts.admin')
@section('title', 'Messages')

@section('content')
<div class="msg-page max-w-6xl">
    <h1 class="msg-title">Messages</h1>
    <p class="msg-sub">Send separate notices to <span>teachers or students</span>, school-wide or for selected classes.</p>

    <div class="grid gap-6 min-[1360px]:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">

        {{-- ═══════════ COMPOSE ═══════════ --}}
        <section class="msg-card overflow-hidden">
            <div class="msg-section-header">
                <div class="msg-section-title">Compose a new message</div>
                <span class="msg-section-sub">Reaches every recipient's inbox instantly</span>
            </div>

            <form method="POST" action="{{ route('admin.messages.store') }}" class="p-6" id="compose-form">
                @csrf

                <div class="mb-5">
                    <p class="msg-field-label">Recipients</p>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label class="msg-audience-option">
                            <input type="radio" name="audience" value="teacher" class="sr-only" @checked(old('audience', 'teacher') === 'teacher')>
                            <span class="msg-audience-icon msg-audience-icon-teacher">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11ZM18.5 6.75h2.75M19.88 5.38v2.75" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-gray-800">Teachers</span>
                                <span class="mt-0.5 block text-xs text-gray-500">Teacher portal inbox</span>
                            </span>
                            <span class="msg-audience-check">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                        </label>
                        <label class="msg-audience-option">
                            <input type="radio" name="audience" value="parent" class="sr-only" @checked(old('audience') === 'parent')>
                            <span class="msg-audience-icon msg-audience-icon-student">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5ZM4.75 20.25h14.5" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-gray-800">Students</span>
                                <span class="mt-0.5 block text-xs text-gray-500">Student portal inbox</span>
                            </span>
                            <span class="msg-audience-check">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="mb-5">
                    <p class="msg-field-label">Delivery area</p>
                    <div class="msg-tab-bar" role="tablist">
                        <label class="msg-tab-btn">
                            <input type="radio" name="delivery_scope" value="all" class="sr-only" @checked(old('delivery_scope', 'all') === 'all')>
                            All classes
                        </label>
                        <label class="msg-tab-btn">
                            <input id="classes-scope" type="radio" name="delivery_scope" value="classes" class="sr-only" @checked(old('delivery_scope') === 'classes')>
                            Selected classes
                        </label>
                    </div>

                    <div id="class-picker" class="msg-class-picker mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @forelse($classes as $class)
                            <label class="msg-class-chip">
                                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" class="sr-only" @checked(in_array($class->id, old('class_ids', [])))>
                                <span class="msg-class-chip-dot"></span>
                                <span class="truncate">{{ $class->name }}</span>
                            </label>
                        @empty
                            <p class="col-span-full text-xs text-gray-500">No classes have been created yet.</p>
                        @endforelse
                    </div>
                    @error('class_ids')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label for="subject" class="msg-field-label">Subject</label>
                    <input id="subject" name="subject" value="{{ old('subject') }}" maxlength="255"
                           placeholder="e.g. Parent-teacher meeting reminder"
                           class="msg-input" required>
                    @error('subject')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <div class="flex items-baseline justify-between">
                        <label for="body" class="msg-field-label">Message</label>
                        <span class="text-[11px] text-gray-400"><span id="body-count">0</span>/5000</span>
                    </div>
                    <textarea id="body" name="body" rows="7" maxlength="5000"
                              placeholder="Write your message here…"
                              class="msg-input resize-none" required>{{ old('body') }}</textarea>
                    @error('body')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="msg-submit-btn" id="compose-submit">
                    <svg class="msg-submit-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.77 59.77 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
                    </svg>
                    <svg class="msg-submit-spinner hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-90" fill="currentColor" d="M21 12a9 9 0 0 0-9-9V0c6.627 0 12 5.373 12 12h-3Z"></path>
                    </svg>
                    <span data-label>Send message</span>
                </button>
            </form>
        </section>

        {{-- ═══════════ SENT MESSAGES ═══════════ --}}
        <section class="msg-card overflow-hidden">
            <div class="msg-section-header">
                <div class="msg-section-title">Sent messages</div>
                @if($messages->total())
                    <span class="msg-count-pill">{{ $messages->total() }}</span>
                @endif
            </div>

            @forelse($messages as $message)
                @php
                    $pct = $message->recipients_count > 0 ? round(($message->read_count / $message->recipients_count) * 100) : 0;
                    $isTeacherMsg = $message->audience === 'teacher';
                @endphp
                <a href="{{ route('admin.messages.show', $message) }}" class="msg-sent-row">
                    <span class="msg-audience-icon {{ $isTeacherMsg ? 'msg-audience-icon-teacher' : 'msg-audience-icon-student' }} shrink-0">
                        @if($isTeacherMsg)
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11ZM18.5 6.75h2.75M19.88 5.38v2.75" />
                            </svg>
                        @else
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5ZM4.75 20.25h14.5" />
                            </svg>
                        @endif
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2">
                            <span class="truncate text-sm font-semibold text-gray-800">{{ $message->subject }}</span>
                        </span>
                        <span class="mt-0.5 line-clamp-2 block text-sm text-gray-500">{{ $message->body }}</span>
                        <span class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-400">
                            <span class="msg-tag {{ $isTeacherMsg ? 'msg-tag-teacher' : 'msg-tag-student' }}">{{ $isTeacherMsg ? 'Teachers' : 'Students' }}</span>
                            <span>{{ $message->target_class_ids ? 'Selected classes' : 'All classes' }}</span>
                            <span>·</span>
                            <span>{{ $message->created_at->diffForHumans() }}</span>
                        </span>
                        <span class="msg-progress mt-2">
                            <span class="msg-progress-fill" style="width:{{ $pct }}%"></span>
                        </span>
                    </span>

                    <span class="flex shrink-0 flex-col items-end gap-1.5">
                        <span class="msg-read-pill">{{ $message->read_count }}/{{ $message->recipients_count }} read</span>
                        <svg class="msg-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </a>
            @empty
                <div class="msg-empty"><p>No messages have been sent yet.</p></div>
            @endforelse

            @if($messages->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $messages->links() }}</div>@endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .msg-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .msg-sub   { font-size: 13px; color: #6b7280; margin: 0 0 28px; }
    .msg-sub span { color: var(--accent); font-weight: 600; }

    /* ── Card shell ── */
    .msg-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .msg-section-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .msg-section-title { font-size: 14px; font-weight: 800; color: #111827; }
    .msg-section-sub { font-size: 11.5px; color: #9ca3af; }

    .msg-field-label {
        font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: 8px;
    }

    /* ── Audience picker ── */
    .msg-audience-option {
        display: flex; align-items: center; gap: 10px;
        border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 12px;
        cursor: pointer; transition: border-color .15s, background .15s, transform .15s;
        position: relative;
    }
    .msg-audience-option:hover { border-color: #c7d2fe; background: #fafafe; transform: translateY(-1px); }
    .msg-audience-option:has(:checked) { border-color: var(--accent-mid); background: #eef2ff; }
    .msg-audience-icon {
        width: 34px; height: 34px; flex-shrink: 0; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
    }
    .msg-audience-icon svg { width: 18px; height: 18px; }
    .msg-audience-icon-teacher { background: #e0e7ff; color: #4338ca; }
    .msg-audience-icon-student { background: #ffedd5; color: #c2410c; }
    .msg-audience-check {
        width: 18px; height: 18px; flex-shrink: 0; border-radius: 999px;
        border: 1.5px solid #d1d5db; background: #fff;
        display: flex; align-items: center; justify-content: center; color: transparent;
        transition: background .15s, border-color .15s, color .15s;
    }
    .msg-audience-check svg { width: 11px; height: 11px; }
    .msg-audience-option:has(:checked) .msg-audience-check {
        background: var(--accent-mid); border-color: var(--accent-mid); color: #fff;
    }

    /* ── Segmented tab bar (delivery scope) ── */
    .msg-tab-bar {
        display: inline-flex; background: #f3f4f6; border-radius: 10px; padding: 3px; gap: 2px;
    }
    .msg-tab-btn {
        padding: 7px 16px; border-radius: 8px; font-size: .8rem; font-weight: 600;
        color: #6b7280; cursor: pointer; transition: all .15s; user-select: none;
    }
    .msg-tab-btn:has(:checked) { background: #fff; color: var(--accent-mid); box-shadow: 0 1px 3px rgba(0,0,0,.1); }
    .msg-tab-btn:hover:not(:has(:checked)) { color: #374151; }

    /* ── Class chips ── */
    .msg-class-picker {
        max-height: 172px; overflow-y: auto; border-radius: 10px;
        border: 1px solid #e5e7eb; background: #f9fafb; padding: 10px;
        transition: opacity .15s;
    }
    .msg-class-picker.is-disabled { opacity: .45; pointer-events: none; }
    .msg-class-chip {
        display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px;
        font-size: .82rem; color: #374151; cursor: pointer; transition: background .12s;
        border: 1px solid transparent;
    }
    .msg-class-chip:hover { background: #fff; }
    .msg-class-chip-dot {
        width: 14px; height: 14px; border-radius: 4px; border: 1.5px solid #d1d5db; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; transition: all .12s;
    }
    .msg-class-chip:has(:checked) { background: #eef2ff; border-color: #c7d2fe; color: var(--accent-mid); font-weight: 600; }
    .msg-class-chip:has(:checked) .msg-class-chip-dot { background: var(--accent-mid); border-color: var(--accent-mid); }
    .msg-class-chip:has(:checked) .msg-class-chip-dot::after {
        content: ''; width: 6px; height: 6px; border-radius: 1px;
        background: #fff; clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    }

    /* ── Inputs ── */
    .msg-input {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 13px;
        font-size: .875rem; color: #111827; background: #fafafa;
        transition: border-color .15s, box-shadow .15s, background .15s; outline: none; box-sizing: border-box;
    }
    .msg-input:focus { border-color: var(--accent-mid); box-shadow: 0 0 0 3px rgba(79,70,229,.12); background: #fff; }

    /* ── Submit button ── */
    .msg-submit-btn {
        width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
        background: var(--accent-mid); color: #fff;
        border: none; border-radius: 11px; padding: 12px; font-size: .88rem; font-weight: 600;
        cursor: pointer; transition: all .2s; box-shadow: 0 4px 14px rgba(79,70,229,.28);
    }
    .msg-submit-btn:hover { background: #4338ca; box-shadow: 0 6px 20px rgba(79,70,229,.38); transform: translateY(-1px); }
    .msg-submit-btn:disabled { opacity: .75; cursor: default; transform: none; }
    .msg-submit-icon, .msg-submit-spinner { width: 17px; height: 17px; }
    .msg-submit-spinner { animation: msg-spin .8s linear infinite; }
    @keyframes msg-spin { to { transform: rotate(360deg); } }

    /* ── Sent list ── */
    .msg-sent-row {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 16px 24px; border-bottom: 1px solid #f3f4f6; text-decoration: none;
        transition: background .12s;
    }
    .msg-sent-row:last-child { border-bottom: none; }
    .msg-sent-row:hover { background: #fafafa; }
    .msg-sent-row:hover .msg-chevron { transform: translateX(2px); color: var(--accent-mid); }
    .msg-chevron { width: 15px; height: 15px; color: #d1d5db; transition: all .15s; }

    .msg-tag {
        display: inline-flex; align-items: center; border-radius: 999px; padding: 1px 8px;
        font-size: 10.5px; font-weight: 700; letter-spacing: .02em;
    }
    .msg-tag-teacher { background: #e0e7ff; color: #4338ca; }
    .msg-tag-student { background: #ffedd5; color: #c2410c; }

    .msg-progress {
        display: block; height: 5px; border-radius: 999px; background: #f3f4f6; overflow: hidden; max-width: 240px;
    }
    .msg-progress-fill {
        display: block; height: 100%; border-radius: 999px;
        background: linear-gradient(90deg, var(--accent-mid), var(--accent));
        transition: width .4s ease;
    }
    .msg-read-pill {
        border-radius: 999px; background: var(--accent-light); color: var(--accent);
        padding: 3px 10px; font-size: 11px; font-weight: 700; white-space: nowrap;
    }
    .msg-count-pill {
        background: #f3f4f6; color: #6b7280; border-radius: 999px;
        padding: 2px 9px; font-size: 11.5px; font-weight: 700;
    }

    .msg-empty { text-align: center; padding: 40px; color: #9ca3af; font-size: 13px; }

    @media (prefers-reduced-motion: reduce) {
        .msg-audience-option, .msg-submit-btn, .msg-chevron, .msg-progress-fill { transition: none; }
        .msg-submit-spinner { animation: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const classScope = document.getElementById('classes-scope');
        const classPicker = document.getElementById('class-picker');
        const updateClassPicker = () => classPicker.classList.toggle('is-disabled', !classScope.checked);
        document.querySelectorAll('input[name="delivery_scope"]').forEach((input) => input.addEventListener('change', updateClassPicker));
        updateClassPicker();

        const body = document.getElementById('body');
        const bodyCount = document.getElementById('body-count');
        const updateCount = () => { bodyCount.textContent = body.value.length; };
        body.addEventListener('input', updateCount);
        updateCount();

        const form = document.getElementById('compose-form');
        const submitBtn = document.getElementById('compose-submit');
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.querySelector('.msg-submit-icon').classList.add('hidden');
            submitBtn.querySelector('.msg-submit-spinner').classList.remove('hidden');
            submitBtn.querySelector('[data-label]').textContent = 'Sending…';
        });
    })();
</script>
@endpush
