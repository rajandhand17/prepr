<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\ComponentAssociation;
use App\Models\LabProgram as ModelsLabProgram;
use App\Models\LabProgramsAchievement;
use App\Models\LabProgramTypeModes;
use App\Models\Organization;
use App\Models\User;
use App\Services\Manage\LabService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LabProgram extends Command
{
    protected $signature = 'migrate-old-data:lab-program';
    protected $description = 'Migrate old Lab Programs table data to new database structure.';

    public function handle()
    {
        $this->info('Starting migration of old Lab Program data...');

        DB::beginTransaction();

        try {
            DB::connection('mysql2')->table('groups')
                ->where('type', 'lab')
                ->chunkById(1000, function ($labPrograms) {
                    foreach ($labPrograms as $labProgram) {
                        $this->processLabProgram($labProgram);
                    }
                });

            DB::commit();
            $this->info('Migration completed successfully.');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error("Migration failed: " . $e->getMessage());
        }
    }

    private function processLabProgram($labProgram)
    {
        $checkUser = User::find($labProgram->user_id);
        $checkOrganization = Organization::find($labProgram->organisation);

        if (!$checkUser || !$checkOrganization) {
            return;
        }

        $category = $this->getCategory($labProgram->category);
        $newLabProgram = ModelsLabProgram::find($labProgram->id) ?? new ModelsLabProgram();

        $this->populateLabProgram($newLabProgram, $labProgram, $category);
        $newLabProgram->save();

        $this->processModesAndTypes($labProgram, $newLabProgram);
        $this->processLabProgramAchievement($labProgram);
        $this->processLabProgramAssociation($labProgram);
    }

    private function getCategory($category)
    {
        if ($category != '0' && $category != null) {
            $checkOldCategory = DB::connection('mysql2')->table('categories')->find($category);
            if ($checkOldCategory) {
                return Category::where('title', $checkOldCategory->name)->first()->id ?? '1';
            }
        }
        return '1';
    }

    private function populateLabProgram($newLabProgram, $labProgram, $category)
    {
        $newLabProgram->fill([
            'id' => $labProgram->id,
            'language' => $labProgram->language,
            'uuid' => Randomize::chars(10)->alphanumeric()->unique()->generate(),
            'title' => $labProgram->title,
            'slug' => UtilityHelper::generateSlug($labProgram->title, new ModelsLabProgram()),
            'description' => $labProgram->description,
            'user_id' => $labProgram->user_id,
            'organization_id' => $labProgram->organisation,
            'category_id' => $category,
            'duration_id' => $this->getTagGroupValue($labProgram, 'duration', ['169' => '1', '170' => '2', '171' => '3', '172' => '4', '173' => '5', '174' => '6']),
            'level_id' => $this->getTagGroupValue($labProgram, 'level', ['157' => '1', '158' => '2', '159' => '3', '160' => '4']),
            'media_type' => 'image',
            'media' => $labProgram->group_image,
            'privacy' => $labProgram->privacy === 'public' ? '0' : '1',
            'status' => '1',
            'is_auto_created' => $labProgram->is_auto_created === '1' ? '1' : '0',
            'is_achievement_enabled' => '1',
            'is_sequential' => '0',
            'is_accessible' => $labProgram->is_accessable,
        ]);
    }

    private function getTagGroupValue($labProgram, $groupType, $map)
    {
        $tagGroup = DB::connection('mysql2')->table('manage_tag_group')
            ->where(['module_id' => $labProgram->id, 'module_type' => 'lab_program', 'group_type' => $groupType])
            ->pluck('group_tag_id')
            ->first();

        $tagGroup = json_decode($tagGroup, true);

        return $map[$tagGroup[0]] ?? null;
    }

    private function processModesAndTypes($labProgram, $newLabProgram)
    {
        $this->processTypeMode($labProgram, 'mode', '1', ['196' => '4', '197' => '5']);
        $this->processTypeMode($labProgram, 'type', '0', ['192' => '0', '193' => '1', '194' => '2', '195' => '3']);
    }

    private function processTypeMode($labProgram, $groupType, $typeMode, $map)
    {
        $values = $this->getTagGroupValue($labProgram, $groupType, $map);
        if ($values) {
            LabProgramTypeModes::where('lab_program_id', $labProgram->id)->where('type_mode', $typeMode)->delete();
            foreach ($values as $value) {
                LabProgramTypeModes::create([
                    'lab_program_id' => $labProgram->id,
                    'type_mode' => $typeMode,
                    'value' => $value,
                ]);
            }
        }
    }

    private function processLabProgramAchievement($labProgram)
    {
        $labProgramAchievement = LabProgramsAchievement::firstOrNew(['lab_program_id' => $labProgram->id]);
        $labProgramAchievement->fill([
            'achievement_name' => $labProgram->prize,
            'achievement_points' => $labProgram->points,
            'achievement_image' => $labProgram->trophy,
        ])->save();
    }

    private function processLabProgramAssociation($labProgram)
    {
        if (!empty($labProgram->lab_id)) {
            $labIds = LabService::getLabIdBasedOnId(explode(',', $labProgram->lab_id));
            if ($labIds) {
                ComponentAssociation::where('lab_program_id', $labProgram->id)->whereIn('lab_id', $labIds)->delete();

                foreach ($labIds as $sequence => $labId) {
                    ComponentAssociation::create([
                        'lab_program_id' => $labProgram->id,
                        'lab_id' => $labId,
                        'sequence' => $sequence + 1,
                    ]);
                }
            }
        }
    }
}
