<?php

namespace App\Console\Commands\SQL;

use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SqlCommandExecution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:sql-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command used to execute SQL command on demand';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Schema::disableForeignKeyConstraints();
            DB::table('users')->whereNull('preferred_timezone')->update(['preferred_timezone' => 'EST']);
            DB::statement("ALTER TABLE `users` CHANGE `preferred_timezone` `preferred_timezone` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'EST'");
            Schema::enableForeignKeyConstraints();
            $this->info('SQL command executed successfully.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error('An error occurred: '.$e->getMessage());
        }
    }
}
