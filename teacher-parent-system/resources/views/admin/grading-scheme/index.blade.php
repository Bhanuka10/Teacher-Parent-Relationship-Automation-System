@extends('layouts.admin')
@section('title', 'Grading Scheme')

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .gr-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .gr-sub   { font-size: 13px; color: #6b7280; margin: 0 0 28px; }
    .gr-sub span { color: var(--accent); font-weight: 600; }

    .gr-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .gr-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; margin-top: 0; border-radius: 9px;
        font-size: 12.5px; font-weight: 700;
        background: var(--accent-mid); color: #fff; border: none;
        text-decoration: none; cursor: pointer;
        box-shadow: 0 4px 14px rgba(79,70,229,.25);
        transition: background .15s;
    }
    .gr-btn:hover { background: #4338ca; }

    .gr-tag {
        display: inline-flex; align-items: center; justify-content: center; gap: 5px;
        min-width: 38px; border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
    }
    .gr-tag .dot { width: 5px; height: 5px; border-radius: 50%; }
    .gr-tag.pass { background: #ccfbf1; color: #0f766e; }
    .gr-tag.pass .dot { background: #0f766e; }
    .gr-tag.fail { background: #ffe4e6; color: #be123c; }
    .gr-tag.fail .dot { background: #be123c; }

    .gr-edit-link { color: var(--accent); }
    .gr-edit-link:hover { color: var(--accent-mid); }

    .gr-empty { text-align: center; padding: 40px; color: #9ca3af; font-size: 13px; }

    .gs-band-row summary::-webkit-details-marker { display: none; }
    .gs-band-row summary::marker { content: ''; }
</style>
@endpush

@section('content')
<div class="max-w-3xl">
    <h1 class="gr-title">Grading scheme</h1>
    <p class="gr-sub">Mark-percentage bands used to grade <span>every subject in every exam</span>, school-wide. Changes apply immediately.</p>

    <section class="gr-card overflow-hidden">
        @forelse($bands as $band)
            <details class="gs-band-row" style="border-bottom:1px solid #f3f4f6">
                <summary style="display:flex;align-items:center;gap:12px;padding:14px 20px;cursor:pointer;list-style:none">
                    <span class="gr-tag {{ $band->is_passing ? 'pass' : 'fail' }}"><span class="dot"></span>{{ $band->grade }}</span>
                    <span class="flex-1 text-sm font-semibold text-gray-700">{{ $band->min_mark }}% – {{ $band->max_mark }}%</span>
                    <span class="text-xs text-gray-400">{{ $band->is_passing ? 'Passing' : 'Failing' }}</span>
                    <span class="text-xs font-semibold gr-edit-link">Edit</span>
                </summary>
                <div style="padding:0 20px 18px">
                    <form method="POST" action="{{ route('admin.grading-scheme.update', $band) }}" class="flex flex-wrap items-end gap-3">
                        @csrf @method('PUT')
                        <div style="width:90px">
                            <label class="ui-field-label" style="margin-bottom:4px">Min %</label>
                            <input type="number" name="min_mark" min="0" max="100" value="{{ $band->min_mark }}" class="ui-input" style="padding:7px 10px" required>
                        </div>
                        <div style="width:90px">
                            <label class="ui-field-label" style="margin-bottom:4px">Max %</label>
                            <input type="number" name="max_mark" min="0" max="100" value="{{ $band->max_mark }}" class="ui-input" style="padding:7px 10px" required>
                        </div>
                        <div style="width:80px">
                            <label class="ui-field-label" style="margin-bottom:4px">Grade</label>
                            <input type="text" name="grade" maxlength="5" value="{{ $band->grade }}" class="ui-input" style="padding:7px 10px" required>
                        </div>
                        <label class="ui-chip" style="margin-bottom:1px">
                            <input type="checkbox" name="is_passing" value="1" class="sr-only" @checked($band->is_passing)>
                            <span>Passing grade</span>
                        </label>
                        <button type="submit" class="gr-btn">Save</button>
                    </form>
                    <form method="POST" action="{{ route('admin.grading-scheme.destroy', $band) }}" class="mt-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">Remove this band</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="gr-empty"><p>No grading bands configured yet.</p></div>
        @endforelse
    </section>

    <section class="gr-card mt-6 p-6">
        <p class="ui-field-label">Add a new band</p>
        <form method="POST" action="{{ route('admin.grading-scheme.store') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div style="width:90px">
                <label class="ui-field-label" style="margin-bottom:4px">Min %</label>
                <input type="number" name="min_mark" min="0" max="100" value="{{ old('min_mark') }}" class="ui-input" style="padding:7px 10px" required>
            </div>
            <div style="width:90px">
                <label class="ui-field-label" style="margin-bottom:4px">Max %</label>
                <input type="number" name="max_mark" min="0" max="100" value="{{ old('max_mark') }}" class="ui-input" style="padding:7px 10px" required>
            </div>
            <div style="width:80px">
                <label class="ui-field-label" style="margin-bottom:4px">Grade</label>
                <input type="text" name="grade" maxlength="5" value="{{ old('grade') }}" class="ui-input" style="padding:7px 10px" required>
            </div>
            <label class="ui-chip" style="margin-bottom:1px">
                <input type="checkbox" name="is_passing" value="1" class="sr-only" @checked(old('is_passing', true))>
                <span>Passing grade</span>
            </label>
            <button type="submit" class="gr-btn">Add band</button>
        </form>
        @error('min_mark')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </section>
</div>
@endsection
