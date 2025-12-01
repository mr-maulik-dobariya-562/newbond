<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToAllTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $databaseName = DB::getDatabaseName();
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = "Tables_in_{$databaseName}";
            if (Schema::hasColumn($table->$tableName, 'branch_id') == false && !in_array($table->$tableName, ['cache', 'cache_locks','failed_jobs', 'jobs', 'job_batches', 'model_has_permissions', 'model_has_roles', 'personal_access_tokens','migrations', 'roles', 'role_has_permissions','password_reset_tokens', 'sessions'])) {
                DB::statement("
                ALTER TABLE `{$table->$tableName}`
                ADD `branch_id` BIGINT(20) UNSIGNED NULL AFTER `id`,
                ADD INDEX `{$table->$tableName}_branch_id_index` (`branch_id`),
                ADD CONSTRAINT `{$table->$tableName}_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;");
            }
        }
    }
}
