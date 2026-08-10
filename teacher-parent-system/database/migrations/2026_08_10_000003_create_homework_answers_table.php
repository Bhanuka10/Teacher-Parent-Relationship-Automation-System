<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('homework_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('homework_question_options')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedSmallInteger('marks_awarded')->nullable();
            $table->timestamps();

            $table->unique(['homework_submission_id', 'homework_question_id'], 'homework_answers_submission_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_answers');
    }
};
