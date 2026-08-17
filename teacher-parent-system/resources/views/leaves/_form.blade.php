{{--
    Shared leave-request form fields, used by both parent create and edit
    pages. Pass an optional $leaveRequest for edit-prefill.
--}}
@php
    $leaveRequest = $leaveRequest ?? null;
@endphp
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label for="lr-start-date" class="ui-field-label">Start date</label>
        <input type="date" id="lr-start-date" name="start_date"
               value="{{ old('start_date', $leaveRequest?->start_date?->format('Y-m-d')) }}" class="ui-input">
        @error('start_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="lr-end-date" class="ui-field-label">End date</label>
        <input type="date" id="lr-end-date" name="end_date"
               value="{{ old('end_date', $leaveRequest?->end_date?->format('Y-m-d')) }}" class="ui-input">
        @error('end_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-4">
    <label for="lr-reason" class="ui-field-label">Reason</label>
    <textarea id="lr-reason" name="reason" rows="4"
              placeholder="e.g. Family trip, medical appointment…"
              class="ui-input resize-none">{{ old('reason', $leaveRequest?->reason) }}</textarea>
    @error('reason')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
</div>
