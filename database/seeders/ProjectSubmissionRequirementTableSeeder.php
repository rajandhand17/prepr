<?php

namespace Database\Seeders;

use App\Models\ProjectSubmissionRequirement;
use Illuminate\Database\Seeder;

class ProjectSubmissionRequirementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectSubmissionRequirements = [
            [
                'title'      => 'Complete project pitch',
                'fr_CA_title'=> 'Présentation complète du projet',
            ], [
                'title'      => 'Complete project tasks',
                'fr_CA_title'=> 'Effectuer les tâches du projet',
            ], [
                'title'      => 'Added Project URL',
                'fr_CA_title'=> 'URL du projet ajoutée',
            ], [
                'title'      => 'Uploaded to project gallery',
                'fr_CA_title'=> 'Téléchargé dans la galerie de projets',
            ], [
                'title'      => 'Uploaded to project files',
                'fr_CA_title'=> 'Téléchargé dans les fichiers de projet',
            ],
        ];

        foreach ($projectSubmissionRequirements as $projectSubmissionRequirement) {
            ProjectSubmissionRequirement::updateOrCreate([
                'title'         => $projectSubmissionRequirement['title'],
                'fr_CA_title'   => $projectSubmissionRequirement['fr_CA_title'],
            ], [
                'title'         => $projectSubmissionRequirement['title'],
                'fr_CA_title'   => $projectSubmissionRequirement['fr_CA_title'],
            ]);
        }
    }
}
