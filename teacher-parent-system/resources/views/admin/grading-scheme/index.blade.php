@extends('layouts.admin')
@section('title', 'Grading Scheme')

@section('content')
<div class="max-w-3xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-teal">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Grading scheme</h1>
            <p class="mt-0.5 text-sm text-gray-500">Mark-percentage bands used to grade every subject in every exam, school-wide. Changes apply immediately.</p>
        </div>
    </div>

    <section class="ui-card overflow-hidden">
        @forelse($bands as $band)
            <details class="gs-band-row" style="border-bottom:1px solid #f3f4f6">
                <summary style="display:flex;align-items:center;gap:12px;padding:14px 20px;cursor:pointer;list-style:none">
                    <span class="ui-tag {{ $band->is_passing ? 'ui-tag-teal' : 'ui-tag-rose' }}" style="min-width:38px;text-align:center">{{ $band->grade }}</span>
                    <span class="flex-1 text-sm font-semibold text-gray-700">{{ $band->min_mark }}% – {{ $band->max_mark }}%</span>
                    <span class="text-xs text-gray-400">{{ $band->is_passing ? 'Passing' : 'Failing' }}</span>
                    <span class="text-xs font-semibold text-indigo-600">Edit</span>
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
                        <button type="submit" class="ui-submit-btn" style="padding:8px 16px;margin-top:0">Save</button>
                    </form>
                    <form method="POST" action="{{ route('admin.grading-scheme.destroy', $band) }}" class="mt-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">Remove this band</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="ui-empty"><p>No grading bands configured yet.</p></div>
        @endforelse
    </section>

    <section class="ui-card mt-6 p-6">
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
            <button type="submit" class="ui-submit-btn" style="padding:8px 16px;margin-top:0">Add band</button>
        </form>
        @error('min_mark')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </section>
</div>

<style>
    .gs-band-row summary::-webkit-details-marker { display: none; }
    .gs-band-row summary::marker { content: ''; }
</style>
@endsection
