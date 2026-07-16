<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number')->unique();
            $table->string('full_name');
            $table->string('index_number')->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('parent_phone_number', 30)->nullable();
            $table->string('parent_email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->foreign('admission_number')
                ->references('admission_number')
                ->on('students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
