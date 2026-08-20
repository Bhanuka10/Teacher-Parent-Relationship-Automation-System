<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::change() compiles to the correct thing per-driver on its own
        // (a plain ALTER...MODIFY on MySQL, a full table rebuild on SQLite) —
        // no raw driver-specific SQL needed, and no doctrine/dbal dependency
        // required (Laravel has done this natively since 11.0).
        Schema::table('attendance', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent', 'leave'])->default('present')->change();
        });
    }

    public function down(): void
    {
        // Reassign any 'leave' rows before narrowing the enum back, or the
        // change() rebuild would fail/truncate rows that no longer fit.
        DB::table('attendance')->where('status', 'leave')->update(['status' => 'absent']);

        Schema::table('attendance', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent'])->default('present')->change();
        });
    }
};
