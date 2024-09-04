<?php

namespace App\Exports\Challenge\Components;

use App\Http\Resources\Manage\Report\ChallengeAssessmentDetailResource;
use App\Models\Challenge;
use App\Services\Manage\Report\ChallengeReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class ChallengeAssessmentReportExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle
{
    private $challenge;
    protected $challengeReportService;

    public function __construct(Challenge $challenge)
    {
        $this->challenge = $challenge;
        $this->challengeReportService = new ChallengeReportService();
    }

    public function title(): string
    {
        return 'Assessment Report';
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        $assessments = $this->challengeReportService->getPaginatedAssessments($this->challenge, false);

        $arr = [
            ['Project Assessors', $assessments['assessor']],
            ['Project Pending Assessment', $assessments['project_pending_assignment']],
            ['Project Accessed', $assessments['project_assessed']],
            ['Winner Selected', $assessments['winner_selected']],
            [''],
        ];

        foreach ($assessments['list'] as $project) {
            $details = $this->challengeReportService->getChallengeAssessmentDetail($this->challenge, $project->id);
            if (data_get($details, 'success')) {
                $projectArr = [
                    [sprintf('Assessment Results - %s', $project->title)],
                    ['Project Title', 'Creator', 'Project Members', 'Assessor', 'Overall Score', 'Overall Comment', 'Criteria', 'Weight', 'Score', 'Comments', 'Achievement'],
                ];

                $arr2 = [data_get($project, 'title'), data_get($project, 'createdBy.full_name'), data_get($details, 'team_members')];

                if (data_get($details, 'users')) {
                    $userAssessments = ChallengeAssessmentDetailResource::collection(data_get($details, 'users'))->toArray(request());

                    for ($i = 0; $i < count($userAssessments); $i++) {
                        if ($i == 0 && $userAssessments[$i]['assessments']) {
                            $arr2 = array_merge($arr2, [data_get($userAssessments[$i], 'full_name'), '0/0', data_get($userAssessments[$i], 'comments'), data_get($userAssessments[$i], 'assessments.0.criteria'), data_get($userAssessments[$i], 'assessments.0.weight'), data_get($userAssessments[$i], 'assessments.0.comment'), 'Participation Award']);
                            $projectArr[] = $arr2;
                        }

                        for ($j = 1; $j < count($userAssessments[$i]['assessments']); $j++) {
                            $arr2 = ['', '', '', '', '', '', data_get($userAssessments[$i], "assessments.$j.criteria"), data_get($userAssessments[$i], "assessments.$j.weight"),
                                data_get($userAssessments[$i], "assessments.$j.score"),
                                data_get($userAssessments[$i], "assessments.$j.comment"), 'Participation Award'];
                            $projectArr[] = $arr2;
                        }
                    }
                }

                $projectArr[] = [''];
                $projectArr[] = [''];

                $arr = array_merge($arr, $projectArr);
            }
        }

        return collect($arr);
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 60,
            'B' => 50,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 50,
        ];
    }
}
