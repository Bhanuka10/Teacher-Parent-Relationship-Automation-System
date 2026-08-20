@extends('layouts.teacher')
@section('title', 'Edit Leave Request')

@push('styles')
<style>
    .ui-back.lr-back { color: #0f766e; }
    .ui-back.lr-back:hover { color: #0d5f58; }
    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 16px 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('teacher.my-leave.index') }}" class="ui-back lr-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to my leave
    </a>

    <h1 class="db-title">Edit leave request</h1>
    <p class="db-sub">Still pending — you can update the dates or reason before admin reviews it.</p>

    <form method="POST" action="{{ route('teacher.my-leave.update', $teacherLeaveRequest) }}" class="mt-6">
        @csrf
        @method('PUT')
        <section class="ui-card p-6">
            @include('leaves._form', ['leaveRequest' => $teacherLeaveRequest])
        </section>

        <button type="submit" class="ui-submit-btn mt-5 w-full justify-center" style="width:100%;background:linear-gradient(135deg,#0f766e,#14b8a6)">Save changes</button>
    </form>
</div>
@endsection
