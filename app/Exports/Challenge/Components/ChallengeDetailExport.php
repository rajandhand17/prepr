<?php

namespace App\Exports\Challenge\Components;

use App\Models\Challenge;
use App\Services\Manage\Report\ChallengeReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class ChallengeDetailExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle
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
        return 'Challenge Details';
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return collect($this->challengeReportService->getDetailExportData($this->challenge));
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 40,
            'C' => 40,
        ];
    }
}
