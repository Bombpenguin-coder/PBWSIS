<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Custom Artisan Command: php artisan db:truncate-all
 * Empties all business tables in the pbwsis database and resets auto-increment IDs to 1 for the live defense.
 */
Artisan::command('db:truncate-all', function () {
    $this->info('Starting full database truncation for live defense...');

    // 1. Disable MySQL Foreign Key checks so parent/child tables don't block each other
    Schema::disableForeignKeyConstraints();

    // 2. Get all tables in the current MySQL database
    $tables = DB::select('SHOW TABLES');
    $dbName = DB::getDatabaseName();
    $columnKey = 'Tables_in_' . $dbName;

    // ADJUSTABLE PARAMETER: Tables listed here will NOT be truncated.
    // Always keep 'migrations' here so Laravel remembers your table structure!
    // (Optional: Add 'users' to this array if your professor allows you to keep your login account ready)
    $protectedTables = [
        'migrations',
    ];

    foreach ($tables as $table) {
        $tableName = $table->$columnKey;

        if (!in_array($tableName, $protectedTables)) {
            DB::table($tableName)->truncate();
            $this->line("Truncated table: <comment>{$tableName}</comment>");
        }
    }

    // 3. Re-enable MySQL Foreign Key security constraints
    Schema::enableForeignKeyConstraints();

    $this->info('SUCCESS! All data has been wiped and all IDs are reset to 1.');
})->purpose('Truncate all database tables for the project defense');