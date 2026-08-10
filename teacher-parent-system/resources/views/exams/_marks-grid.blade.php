{{--
    Shared marks grid, used by both the teacher marks-entry/grading page and
    the admin read-only oversight page. Pass:
    - $exam    (Exam, with subjects loaded)
    - $grid    (Collection from ExamResultService::gridFor())
    - $canEdit (bool) — teacher gets <input> cells, admin gets read-only text
--}}
@php
    $subjects = $exam->subjects;
@endphp
<div class="ui-grid-scroll">
    <table style="width:100%;border-collapse:collapse;min-width:{{ 320 + $subjects->count() * 110 }}px">
        <thead>
            <tr style="border-bottom:1.5px solid #e5e7eb">
                <th style="text-align:left;padding:10px 12px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap">Student</th>
                @foreach($subjects as $subject)
                    <th style="text-align:center;padding:10px 8px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap">
                        {{ $subject->name }}<br><span style="font-weight:500;text-transform:none;color:#c1c5cc">/ {{ $subject->max_mark }}</span>
                    </th>
                @endforeach
                <th style="text-align:center;padding:10px 8px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap">Average</th>
                <th style="text-align:center;padding:10px 12px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap">Rank</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grid as $rIndex => $row)
                @php
                    $student = $row['student'];
                    $initials = collect(explode(' ', $student->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                    $rank = $row['class_rank'];
                    $rankClass = match(true) {
                        $rank === null => 'ui-rank-none',
                        $rank === 1 => 'ui-rank-1',
                        $rank === 2 => 'ui-rank-2',
                        $rank === 3 => 'ui-rank-3',
                        default => 'ui-rank-other',
                    };
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:8px 12px">
                        <div style="display:flex;align-items:center;gap:9px">
                            <span class="ui-avatar ui-avatar-indigo" style="width:28px;height:28px;font-size:11px">{{ strtoupper($initials) }}</span>
                            <span style="font-size:.84rem;font-weight:600;color:#1f2937;white-space:nowrap">{{ $student->name }}</span>
                        </div>
                    </td>
                    @foreach($subjects as $cIndex => $subject)
                        @php($cell = $row['marks'][$subject->id])
                        <td style="text-align:center;padding:8px">
                            @if($canEdit)
                                <input type="number" inputmode="numeric" min="0" max="{{ $subject->max_mark }}"
                                       name="marks[{{ $student->id }}][{{ $subject->id }}]"
                                       value="{{ old("marks.{$student->id}.{$subject->id}", $cell['mark']) }}"
                                       data-row="{{ $rIndex }}" data-col="{{ $cIndex }}"
                                       class="exam-mark-input ui-input" style="width:64px;text-align:center;padding:7px 6px;margin:0 auto">
                                @error("marks.{$student->id}.{$subject->id}")
                                    <div style="font-size:10px;color:#dc2626;margin-top:2px;max-width:80px">{{ $message }}</div>
                                @enderror
                            @else
                                @if($cell['mark'] !== null)
                                    <span style="font-weight:600;color:#1f2937">{{ $cell['mark'] }}</span>
                                    @if($cell['grade'])
                                        <span class="ui-tag {{ $cell['grade']['is_passing'] ? 'ui-tag-teal' : 'ui-tag-rose' }}" style="margin-left:4px">{{ $cell['grade']['grade'] }}</span>
                                    @endif
                                @else
                                    <span style="color:#d1d5db">—</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align:center;padding:8px;font-weight:700;color:#111827">
                        {{ $row['average'] !== null ? $row['average'] : '—' }}
                    </td>
                    <td style="text-align:center;padding:8px 12px">
                        <span class="ui-rank-badge {{ $rankClass }}">{{ $rank ?? '—' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $subjects->count() + 3 }}" class="ui-empty">No students in this class yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@error('marks')
    <p class="mt-3 text-xs font-medium text-red-600">{{ $message }}</p>
@enderror
