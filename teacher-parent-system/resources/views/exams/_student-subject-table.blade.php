{{--
    Parent-facing subject breakdown: one row per subject (the inverse shape of
    the teacher/admin marks-grid, which is one row per student). Pass:
    - $exam ($exam->subjects loaded)
    - $row (this student's row from ExamResultService::gridFor())
    - $subjectAverages (Collection: exam_subject_id => class average mark)
--}}
<div class="ui-grid-scroll">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:1.5px solid #e5e7eb">
                <th style="text-align:left;padding:9px 12px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em">Subject</th>
                <th style="text-align:center;padding:9px 8px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em">Mark</th>
                <th style="text-align:center;padding:9px 8px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em">Grade</th>
                <th style="text-align:center;padding:9px 8px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em">Class avg</th>
                <th style="text-align:center;padding:9px 12px;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.03em">Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exam->subjects as $subject)
                @php
                    $cell = $row['marks'][$subject->id];
                    $rank = $row['subject_ranks'][$subject->id] ?? null;
                    $rankClass = match(true) {
                        $rank === null => 'ui-rank-none',
                        $rank === 1 => 'ui-rank-1',
                        $rank === 2 => 'ui-rank-2',
                        $rank === 3 => 'ui-rank-3',
                        default => 'ui-rank-other',
                    };
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:10px 12px;font-size:.86rem;font-weight:600;color:#1f2937">{{ $subject->name }}</td>
                    <td style="text-align:center;padding:10px 8px;font-size:.86rem;font-weight:700;color:#111827">
                        {{ $cell['mark'] !== null ? $cell['mark'].' / '.$subject->max_mark : '—' }}
                    </td>
                    <td style="text-align:center;padding:10px 8px">
                        @if($cell['grade'])
                            <span class="ui-tag {{ $cell['grade']['is_passing'] ? 'ui-tag-teal' : 'ui-tag-rose' }}">{{ $cell['grade']['grade'] }}</span>
                        @else
                            <span style="color:#d1d5db">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;padding:10px 8px;font-size:.8rem;color:#6b7280">
                        {{ $subjectAverages[$subject->id] ?? '—' }}
                    </td>
                    <td style="text-align:center;padding:10px 12px">
                        <span class="ui-rank-badge {{ $rankClass }}" style="width:28px;height:28px;font-size:11.5px">{{ $rank ?? '—' }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
