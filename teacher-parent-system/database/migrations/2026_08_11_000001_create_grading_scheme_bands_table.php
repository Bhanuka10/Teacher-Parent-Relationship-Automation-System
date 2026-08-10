<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_scheme_bands', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('min_mark');
            $table->unsignedTinyInteger('max_mark');
            $table->string('grade', 5);
            $table->boolean('is_passing')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('grading_scheme_bands')->insert([
            ['min_mark' => 75, 'max_mark' => 100, 'grade' => 'A', 'is_passing' => true, 'position' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['min_mark' => 65, 'max_mark' => 74, 'grade' => 'B', 'is_passing' => true, 'position' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['min_mark' => 55, 'max_mark' => 64, 'grade' => 'C', 'is_passing' => true, 'position' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['min_mark' => 35, 'max_mark' => 54, 'grade' => 'S', 'is_passing' => true, 'position' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['min_mark' => 0, 'max_mark' => 34, 'grade' => 'F', 'is_passing' => false, 'position' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_scheme_bands');
    }
};
