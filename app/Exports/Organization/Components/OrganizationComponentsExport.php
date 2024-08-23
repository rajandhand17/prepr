<?php

namespace App\Exports\Organization\Components;

use App\Models\Organization;
use App\Services\Manage\Report\ChallengePathReportService;
use App\Services\Manage\Report\ChallengeReportService;
use App\Services\Manage\Report\LabProgramReportService;
use App\Services\Manage\Report\LabReportService;
use App\Services\Manage\Report\ResourceReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class OrganizationComponentsExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle
{
    private $organization;
    protected LabReportService $labReportService;
    protected LabProgramReportService $labProgramReportService;
    protected ChallengeReportService $challengeReportService;
    protected ResourceReportService $resourceReportService;
    protected ChallengePathReportService $challengePathReportService;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
        $this->labReportService = new LabReportService();
        $this->labProgramReportService = new LabProgramReportService();
        $this->challengeReportService = new ChallengeReportService();
        $this->resourceReportService = new ResourceReportService();
        $this->challengePathReportService = new ChallengePathReportService();
    }

    public function title(): string
    {
        return 'List of Linked Components';
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        $arr = [
            ['Component Details'],
        ];

        $resources = [
            'Labs'                 => $this->organization->labs()->get(),
            'Lab Programs'         => $this->organization->lab_programs_count()->get(),
            'Challenges'           => $this->organization->challenges_count()->get(),
            'Challenge Paths'      => $this->organization->challenge_paths_count()->get(),
            'Resource Modules'     => $this->organization->resource_modules_count()->get(),
            'Resource Groups'      => $this->organization->resource_groups_count()->get(),
            'Resource Collections' => $this->organization->resource_collections_count()->get(),
        ];

        foreach ($resources as $type => $items) {
            $arr[] = [$type];

            if ($items->isEmpty()) {
                $arr[] = [sprintf('No %s is associated', $type)];
            } else {
                if ($type === 'Challenges' || $type === 'Labs') {
                    $arr[] = ['Title', 'URL', 'Total Members', 'Not Started', 'In Progress', 'Completed', sprintf('%s Report URL', $type)];
                } else {
                    $arr[] = ['Title', 'URL', 'Total Members', 'Not Started', 'In Progress', 'Completed'];
                }

                foreach ($items as $item) {
                    $progressData = $this->getProgressData($type, $item);

                    $row = [
                        $item->title,
                        $this->getLink($type, $item),
                        $progressData['total'] ?? 0,
                        $progressData['not_started'] ?? 0,
                        $progressData['in_progress'] ?? 0,
                        $progressData['completed'] ?? 0,
                    ];

                    if ($type === 'Challenges') {
                        $row[] = $this->getReportLink($type, $item);
                    }

                    $arr[] = $row;
                }
            }

            $arr[] = [''];
        }

        $arr[] = [''];

        return collect($arr);
    }

    private function getProgressData(string $type, $item): array|false
    {
        return match ($type) {
            'Labs'                  => $this->labReportService->labMemberProgress($item),
            'Lab Programs'          => $this->labProgramReportService->getLabProgramMemberProgress($item),
            'Challenges'            => $this->challengeReportService->getChallengeMemberProgress($item),
            'Challenge Paths'       => $this->challengePathReportService->getChallengePathMemberProgress($item),
            'Resource Module'       => $this->resourceReportService->getResourceModuleMemberProgress($item),
            'Resource Group'        => $this->resourceReportService->getResourceGroupMemberProgress($item),
            'Resource Collection'   => $this->resourceReportService->getResourceCollectionMemberProgress($item),
            default                 => false,
        };
    }

    private function getLink(string $type, $item): string
    {
        $values = [
            'Labs'                 => 'lab',
            'Lab Programs'         => 'lab-program',
            'Challenges'           => 'challenge',
            'Challenge Paths'      => 'challenge-path',
            'Resource Modules'     => 'resource',
            'Resource Collections' => 'resource-collection',
            'Resource Groups'      => 'resource-group',
        ];

        return sprintf('%s/%s/%s', env('FRONTEND_SITE_URL'), $values[$type], $item->slug);
    }

    private function getReportLink(string $type, $item): string
    {
        $values = [
            'Labs'                 => 'lab',
            'Challenges'           => 'challenge',
            'Challenge Paths'      => 'challenge-path',
            'Lab Programs'         => 'lab-program',
            'Resource Modules'     => 'resource',
            'Resource Collections' => 'resource-collection',
            'Resource Groups'      => 'resource-group',
        ];

        return sprintf('%s/report/%s/%s', env('FRONTEND_SITE_URL'), $values[$type], $item->slug);
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 100,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 100,
        ];
    }
}
