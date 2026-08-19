@extends('layouts.teacher')
@section('title', 'Edit Leave Request')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('teacher.my-leave.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to my leave
    </a>

    <h1 class="mt-4 text-2xl font-bold text-gray-800">Edit leave request</h1>
    <p class="mt-1 text-sm text-gray-500">Still pending — you can update the dates or reason before admin reviews it.</p>

    <form method="POST" action="{{ route('teacher.my-leave.update', $teacherLeaveRequest) }}" class="mt-6">
        @csrf
        @method('PUT')
        <section class="ui-card p-6">
            @include('leaves._form', ['leaveRequest' => $teacherLeaveRequest])
        </section>

        <button type="submit" class="ui-submit-btn mt-5 w-full justify-center" style="width:100%">Save changes</button>
    </form>
</div>
@endsection
