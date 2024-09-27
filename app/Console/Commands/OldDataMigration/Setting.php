<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Setting as Settings; // Ensure you import the correct model
use DB;
use Illuminate\Console\Command;

class Setting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old setting table data to new database structure.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $insertArr = [];
            $this->info('Starting migration for the settings table...');
            DB::beginTransaction();

            DB::connection('mysql2')->table('settings')->chunkById(1000, function ($settings) use (&$insertArr) {
                foreach ($settings as $setting) {
                    $moduleType = $this->mapSettingType($setting->type); // Use the mapped ENUM value here

                    if ($moduleType !== null) {
                        $settingDetails = [
                            'id'         => $setting->id,
                            'code'       => $setting->code,
                            'module_type'=> $moduleType, // Insert the ENUM value instead of the type string
                            'label'      => $setting->label,
                            'value'      => $setting->value,
                            'created_at' => $setting->created_at,
                            'updated_at' => $setting->updated_at,
                        ];

                        // Check if the setting already exists before inserting
                        if (!Settings::where('id', $setting->id)->exists()) {
                            $insertArr[] = $settingDetails;
                        }
                    }
                }

                if (!empty($insertArr)) {
                    Settings::insert($insertArr);
                    $insertArr = []; // Clear the array after each chunk is inserted
                }
            });

            DB::commit();
            $this->info('Settings migration completed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error during migration: ' . $e->getMessage());
        }
    }

    /**
     * Map setting type from old structure to new type.
     *
     * @param string $type
     * @return string|null
     */
    private function mapSettingType($type)
    {
        // Return the corresponding ENUM value
        $types = [
            'BOOLEAN'   => '0',
            'NUMBER'    => '1',
            'DATE'      => '2',
            'TEXT'      => '3',
            'SELECT'    => '4',
            'FILE'      => '5',
            'TEXTAREA'  => '6',
        ];

        return $types[$type] ?? null;
    }
}
