<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE attendance MODIFY status ENUM('present','absent','leave') NOT NULL DEFAULT 'present'");
    }

    public function down(): void
    {
        DB::statement("UPDATE attendance SET status = 'absent' WHERE status = 'leave'");
        DB::statement("ALTER TABLE attendance MODIFY status ENUM('present','absent') NOT NULL DEFAULT 'present'");
    }
};
