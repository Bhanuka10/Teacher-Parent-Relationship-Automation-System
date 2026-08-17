<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'student_id', 'school_class_id', 'requested_by',
        'start_date', 'end_date', 'reason',
        'status', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function dayCount(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /** @return array<int, \Illuminate\Support\Carbon> */
    public function dateRange(): array
    {
        $dates = [];
        $cursor = $this->start_date->copy();

        while ($cursor->lte($this->end_date)) {
            $dates[] = $cursor->copy();
            $cursor->addDay();
        }

        return $dates;
    }
}
