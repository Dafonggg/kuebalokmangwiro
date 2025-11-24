<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // For MySQL/MariaDB, modify the enum column
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'qris', 'bank_transfer', 'manual') DEFAULT 'cash'");
        }
        // For SQLite, enum is stored as text, so no schema change needed
        // Validation in controllers handles the constraint
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Revert to original enum values
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'qris', 'manual') DEFAULT 'cash'");
        }
    }
};
