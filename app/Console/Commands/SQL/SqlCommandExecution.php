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
            // Step 1: Create a temporary table and update duplicate certificate_numbers
            DB::statement("
                CREATE TEMPORARY TABLE DuplicateCerts AS
                SELECT id, certificate_number,
                       (@row_num := IF(@prev_cert = certificate_number, @row_num + 1, 1)) AS row_num,
                       @prev_cert := certificate_number
                FROM user_achievements, (SELECT @row_num := 0, @prev_cert := '') AS vars
                WHERE certificate_number IN (
                    SELECT certificate_number
                    FROM user_achievements
                    GROUP BY certificate_number
                    HAVING COUNT(*) > 1
                )
                ORDER BY certificate_number, id
            ");

            // Step 2: Update duplicate certificate_number values
            DB::table('user_achievements as ua')
            ->join('DuplicateCerts as dc', 'ua.id', '=', 'dc.id')
                ->where('dc.row_num', '>', 1)
                ->update([
                    'ua.certificate_number' => DB::raw("CONCAT(LEFT(dc.certificate_number, LENGTH(dc.certificate_number) - 3), LPAD(dc.row_num, 3, '0'))"),
                ]);

            // Step 3: Get the updated results
            $results = DB::table('user_achievements')
            ->select('id', 'certificate_number')
            ->whereIn('certificate_number', function ($query) {
                $query->select(DB::raw('DISTINCT LEFT(certificate_number, LENGTH(certificate_number) - 3) AS base_cert'))
                ->from('user_achievements')
                ->groupBy('certificate_number')
                ->havingRaw('COUNT(*) > 1');
            })
                ->orderBy('certificate_number')
                ->orderBy('id')
                ->get();

            Schema::enableForeignKeyConstraints();

            // Output the results in a readable format
            if ($results->isEmpty()) {
                $this->info('No duplicate certificate numbers found or updated.');
            } else {
                foreach ($results as $result) {
                    $this->line("ID: {$result->id}, Certificate Number: {$result->certificate_number}");
                }
            }

            DB::statement("UPDATE `user_achievements` SET `certificate_number` = '2309050002' WHERE `user_achievements`.`id` = 7756");

            DB::table('challenge_announcement_recipients')->truncate();
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
