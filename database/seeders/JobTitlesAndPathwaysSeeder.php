<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use App\Models\JobTitlePathway;
use App\Models\RelatedPathway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JobTitlesAndPathwaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $jsonPath = base_path('database/jobTitlePathways.json');
        $jsonData = json_decode(File::get($jsonPath), true);

        foreach ($jsonData['data'] as $item) {
            JobTitlePathway::updateOrCreate(
                ['lightcast_pathway_id' => $item['id']],
                [
                    'name'        => $item['name'],
                    'fr_CA_name'  => $item['fr_CA_name'] ?? null,
                    'job_level'   => $item['jobLevel'],
                    'mean_salary' => $item['meanSalary'],
                ]
            );
        }

        $jsonPath = base_path('database/jobTitles.json');
        $jsonData = json_decode(File::get($jsonPath), true);

        foreach ($jsonData['data'] as $item) {
            $pathway = JobTitlePathway::where('lightcast_pathway_id', $item['pathway_id'] ?? null)->first();

            JobTitle::updateOrCreate(
                ['lightcast_id' => $item['id']],
                [
                    'uuid'        => Str::uuid(),
                    'title'       => $item['name'],
                    'fr_CA_title' => $item['fr_CA_name'] ?? null,
                    'pathway_id'  => $pathway ? $pathway->id : null,
                ]
            );
        }

        $jsonPath = base_path('database/jobTitlePathways.json');
        $jsonData = json_decode(File::get($jsonPath), true);

        foreach ($jsonData['data'] as $item) {
            if (isset($item['related_pathways']) && is_array($item['related_pathways'])) {
                foreach ($item['related_pathways'] as $relatedPathway) {
                    foreach ($relatedPathway as $id => $category) {
                        $mainPathwayExists = JobTitlePathway::where('lightcast_pathway_id', $item['id'])->exists();
                        $relatedPathwayExists = JobTitlePathway::where('lightcast_pathway_id', $id)->exists();

                        $relationshipExists = RelatedPathway::where('lightcast_pathway_id', $item['id'])
                            ->where('related_lightcast_pathway_id', $id)
                            ->where('category', $category)
                            ->exists();

                        if ($mainPathwayExists && $relatedPathwayExists && !$relationshipExists) {
                            RelatedPathway::create([
                                'lightcast_pathway_id'         => $item['id'],
                                'related_lightcast_pathway_id' => $id,
                                'category'                     => $category,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
