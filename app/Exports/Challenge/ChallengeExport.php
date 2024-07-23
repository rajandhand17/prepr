<?php

namespace App\Exports\Challenge;

use App\Exports\Challenge\Components\ChallengeAssessmentReportExport;
use App\Exports\Challenge\Components\ChallengeComponentsExport;
use App\Exports\Challenge\Components\ChallengeDetailExport;
use App\Exports\Challenge\Components\ChallengeMembersExport;
use App\Models\Challenge;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ChallengeExport implements WithMultipleSheets
{
    private $challenge;

    public function __construct(Challenge $challenge)
    {
        $this->challenge = $challenge;
    }

    /**
     * Returns an array of sheets to be included in the export.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new ChallengeDetailExport($this->challenge),
            new ChallengeComponentsExport($this->challenge),
            new ChallengeMembersExport($this->challenge),
            new ChallengeAssessmentReportExport($this->challenge),
        ];
    }
}
