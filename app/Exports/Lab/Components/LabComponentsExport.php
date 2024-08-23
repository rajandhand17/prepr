<?php

namespace App\Exports\Lab\Components;

use App\Models\Lab;
use App\Services\Manage\Report\ChallengePathReportService;
use App\Services\Manage\Report\ChallengeReportService;
use App\Services\Manage\Report\ResourceReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class LabComponentsExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle
{
    private $lab;
    protected ChallengeReportService $challengeReportService;
    protected ResourceReportService $resourceReportService;
    protected ChallengePathReportService $challengePathReportService;

    public function __construct(Lab $lab)
    {
        $this->lab = $lab;
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
            'Challenges'           => $this->lab->challenges()->get(),
            'Challenge Paths'      => $this->lab->challengePaths()->get(),
            'Resource Modules'     => $this->lab->resourceModules()->get(),
            'Resource Groups'      => $this->lab->resourceGroups()->get(),
            'Resource Collections' => $this->lab->resourceCollections()->get(),
        ];

        foreach ($resources as $type => $items) {
            $arr[] = [$type];

            if ($items->isEmpty()) {
                $arr[] = [sprintf('No %s is associated', $type)];
            } else {
                if ($type === 'Challenges') {
                    $arr[] = ['Title', 'URL', 'Total Members', 'Not Started', 'In Progress', 'Completed', 'Late Submission', 'Deadline Missed', 'Challenge Report URL'];
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
                        $row = array_merge($row, [
                            $progressData['late_submission'] ?? 0,
                            $progressData['deadline_missed'] ?? 0,
                            $this->getReportLink($type, $item),
                        ]);
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
            'Challenges'          => $this->challengeReportService->getChallengeMemberProgress($item),
            'Challenge Paths'     => $this->challengePathReportService->getChallengePathMemberProgress($item),
            'Resource Module'     => $this->resourceReportService->getResourceModuleMemberProgress($item),
            'Resource Group'      => $this->resourceReportService->getResourceGroupMemberProgress($item),
            'Resource Collection' => $this->resourceReportService->getResourceCollectionMemberProgress($item),
            default               => false,
        };
    }

    private function getLink(string $type, $item): string
    {
        $values = [
            'Challenges'           => 'challenge',
            'Challenge Paths'      => 'challenge-path',
            'Lab Programs'         => 'lab-program',
            'Resource Modules'     => 'resource',
            'Resource Collections' => 'resource-collection',
            'Resource Groups'      => 'resource-group',
        ];

        return sprintf('%s/%s/%s', env('FRONTEND_SITE_URL'), $values[$type], $item->slug);
    }

    private function getReportLink(string $type, $item): string
    {
        $values = [
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
