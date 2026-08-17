<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Exam;
use App\Models\GradingSchemeBand;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Collection;

class ExamResultService
{
    private ?Collection $bandsCache = null;

    /**
     * Grade + pass/fail for a mark, normalized as a percentage of the subject's own max mark.
     */
    public function gradeFor(?int $mark, int $subjectMaxMark): ?array
    {
        if ($mark === null || $subjectMaxMark <= 0) {
            return null;
        }

        $percentage = ($mark / $subjectMaxMark) * 100;

        $band = $this->bands()->first(
            fn (GradingSchemeBand $band) => $percentage >= $band->min_mark && $percentage <= $band->max_mark
        );

        return $band ? ['grade' => $band->grade, 'is_passing' => $band->is_passing] : null;
    }

    /**
     * Full marks grid for an exam: one row per student in the exam's class.
     * class_rank and subject_ranks are only populated for students with a mark
     * for every subject in the exam — partial entries are excluded from ranking entirely.
     */
    public function gridFor(Exam $exam): Collection
    {
        $subjects = $exam->subjects()->with('marks')->get();
        $students = $exam->schoolClass->students()->orderBy('name')->get();
        $subjectCount = $subjects->count();

        $rows = $students->map(function (Student $student) use ($subjects, $subjectCount) {
            $marksBySubject = [];
            $enteredCount = 0;
            $total = 0;

            foreach ($subjects as $subject) {
                $mark = $subject->marks->firstWhere('student_id', $student->id)?->mark;

                if ($mark !== null) {
                    $enteredCount++;
                    $total += $mark;
                }

                $marksBySubject[$subject->id] = [
                    'mark' => $mark,
                    'grade' => $this->gradeFor($mark, $subject->max_mark),
                ];
            }

            $isComplete = $subjectCount > 0 && $enteredCount === $subjectCount;

            return [
                'student' => $student,
                'marks' => $marksBySubject,
                'total' => $total,
                'average' => $enteredCount > 0 ? round($total / $subjectCount, 2) : null,
                'is_complete' => $isComplete,
                'class_rank' => null,
                'subject_ranks' => [],
            ];
        });

        $completeRows = $rows->filter(fn ($row) => $row['is_complete']);

        $classRanks = $this->competitionRank(
            $completeRows->map(fn ($row) => ['key' => $row['student']->id, 'value' => $row['average']])
        );

        $rows = $rows->map(function ($row) use ($classRanks) {
            $row['class_rank'] = $classRanks->get($row['student']->id);

            return $row;
        });

        foreach ($subjects as $subject) {
            $subjectRanks = $this->competitionRank(
                $completeRows->map(fn ($row) => ['key' => $row['student']->id, 'value' => $row['marks'][$subject->id]['mark']])
            );

            $rows = $rows->map(function ($row) use ($subject, $subjectRanks) {
                $row['subject_ranks'][$subject->id] = $subjectRanks->get($row['student']->id);

                return $row;
            });
        }

        return $rows;
    }

    /**
     * Single-student performance summary for one exam.
     */
    public function summaryFor(Exam $exam, Student $student): array
    {
        $row = $this->gridFor($exam)->first(fn ($row) => $row['student']->id === $student->id);

        if (!$row) {
            return [];
        }

        $subjects = $exam->subjects()->get()->keyBy('id');
        $totalRanked = 0;
        $highestSubject = null;
        $lowestSubject = null;
        $bestPercent = null;
        $worstPercent = null;
        $gradeCounts = [];
        $passedCount = 0;
        $enteredCount = 0;

        foreach ($row['marks'] as $subjectId => $data) {
            if ($data['mark'] === null) {
                continue;
            }

            $enteredCount++;
            $subject = $subjects->get($subjectId);
            $percent = $subject->max_mark > 0 ? ($data['mark'] / $subject->max_mark) * 100 : 0;

            if ($bestPercent === null || $percent > $bestPercent) {
                $bestPercent = $percent;
                $highestSubject = $subject->name;
            }

            if ($worstPercent === null || $percent < $worstPercent) {
                $worstPercent = $percent;
                $lowestSubject = $subject->name;
            }

            if ($data['grade']) {
                $gradeCounts[$data['grade']['grade']] = ($gradeCounts[$data['grade']['grade']] ?? 0) + 1;

                if ($data['grade']['is_passing']) {
                    $passedCount++;
                }
            }
        }

        return [
            'average' => $row['average'],
            'class_rank' => $row['class_rank'],
            'is_complete' => $row['is_complete'],
            'highest_subject' => $highestSubject,
            'lowest_subject' => $lowestSubject,
            'subjects_passed' => "{$passedCount}/{$enteredCount}",
            'grade_counts' => $gradeCounts,
        ];
    }

    /**
     * Chronological (academic_year, term) list of a student's exams within a class,
     * each with average/rank plus the delta vs the previous term's average.
     */
    public function termHistoryFor(Student $student, SchoolClass $schoolClass): Collection
    {
        $exams = Exam::where('school_class_id', $schoolClass->id)
            ->orderBy('academic_year')
            ->orderBy('term')
            ->get();

        $previousAverage = null;

        return $exams->map(function (Exam $exam) use ($student, &$previousAverage) {
            $summary = $this->summaryFor($exam, $student);
            $average = $summary['average'] ?? null;
            $delta = ($average !== null && $previousAverage !== null) ? round($average - $previousAverage, 2) : null;

            if ($average !== null) {
                $previousAverage = $average;
            }

            return [
                'exam' => $exam,
                'average' => $average,
                'class_rank' => $summary['class_rank'] ?? null,
                'delta' => $delta,
            ];
        });
    }

    /**
     * Present/absent/percentage attendance within the exam's term date window.
     */
    public function attendanceCorrelationFor(Exam $exam, Student $student): array
    {
        $records = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$exam->term_start_date, $exam->term_end_date])
            ->get();

        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $leave = $records->where('status', 'leave')->count();
        $total = $present + $absent; // leave is excused, excluded from the rate

        return [
            'present' => $present,
            'absent' => $absent,
            'leave' => $leave,
            'total' => $total,
            'percent' => $total > 0 ? round(($present / $total) * 100, 1) : null,
        ];
    }

    /**
     * Standard "1224" competition ranking: ties share a rank, the next distinct
     * value resumes at its true sorted position (not the next integer).
     */
    private function competitionRank(Collection $items): Collection
    {
        $sorted = $items->sortByDesc('value')->values();
        $ranks = collect();

        foreach ($sorted as $index => $item) {
            if ($index > 0 && $sorted[$index - 1]['value'] == $item['value']) {
                $rank = $ranks->get($sorted[$index - 1]['key']);
            } else {
                $rank = $index + 1;
            }

            $ranks->put($item['key'], $rank);
        }

        return $ranks;
    }

    private function bands(): Collection
    {
        return $this->bandsCache ??= GradingSchemeBand::orderBy('position')->get();
    }
}
