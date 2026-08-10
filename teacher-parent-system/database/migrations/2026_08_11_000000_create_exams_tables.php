<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('academic_year', 9);
            $table->unsignedTinyInteger('term');
            $table->string('name');
            $table->date('exam_date');
            $table->date('term_start_date');
            $table->date('term_end_date');
            $table->timestamps();

            $table->index(['school_class_id', 'academic_year', 'term']);
        });

        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('max_mark')->default(100);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['exam_id', 'name'], 'exam_subjects_exam_id_name_unique');
        });

        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedSmallInteger('mark')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_subject_id', 'student_id'], 'exam_marks_exam_subject_id_student_id_unique');
            $table->index(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_subjects');
        Schema::dropIfExists('exams');
    }
};
