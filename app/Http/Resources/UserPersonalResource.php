<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPersonalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userTypeMap = [
            '0'  => 'Employee',
            '1'  => 'Investor',
            '2'  => 'Teacher',
            '3'  => 'Job Seeker',
            '4'  => 'Student',
            '5'  => 'Recent Grad',
            '6'  => 'Expert',
            '7'  => 'Employer',
            '8'  => 'Recent Grad',
            '9'  => 'Facilitator',
            '10' => 'Job Seeker',
            '11' => 'Startup',
            '12' => 'Learner',
            '13' => 'Mentor',
            '14' => 'Innovator',
            '15' => 'Aspiring Entrepreneur',
            '16' => 'Evaluator',
            '17' => 'Small',
            '18' => 'Entrepreneur',
            '19' => 'Ngo',
            '20' => 'Enterprise',
            '21' => 'Applicant',
            '22' => 'Educational',
            '23' => 'Community',
            '24' => 'Educator',
            '25' => 'Government',
            '26' => 'Others',
        ];

        return [
            'about'     => $this->about,
            'age'       => $this->age,
            'user_type' => $userTypeMap[$this->user_type] ?? '',
        ];
    }
}
