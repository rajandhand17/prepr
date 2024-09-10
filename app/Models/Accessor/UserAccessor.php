<?php

namespace App\Models\Accessor;

trait UserAccessor
{
    /**
     * @return array
     */
    public function getFormattedCompletionCountByModuleAttribute(): array
    {
        $totalLabCompleted = $this->labsProgress()
            ->where('percentage', '=', '100')->orWhere('is_completed', '=', '1')->orWhere('status', '=', '2')->count();

        $totalResourceCompleted = $this->resourcesModulesProgresses()
            ->where('percentage', '=', '100')->orWhere('is_completed', '=', '1')->orWhere('status', '=', '2')
            ->count();

        $totalChallengeCompleted = $this->challengesProgress()->where('percentage', '=', '100')->orWhere('is_completed', '=', '1')->orWhere('status', '=', '2')->count();

        return [
            'total_lab_completed'       => $totalLabCompleted,
            'total_resource_completed'  => $totalResourceCompleted,
            'total_challenge_completed' => $totalChallengeCompleted,
        ];
    }

    public function getFormattedLastLoginDateAttribute()
    {
        $lastLogin = $this->userActivities()
            ->where('activity_type', 'login')
            ->latest('created_at')
            ->first();

        return $lastLogin ? $lastLogin->created_at : null;
    }

    public function getFormattedLoginStatusAttribute(): string
    {
        $lastLogin = $this->userActivities()
            ->where('activity_type', 'login')
            ->latest()
            ->value('created_at');

        if (!$lastLogin) {
            return 'No login activity found';
        }

        $diffInDays = $lastLogin->diffInDays(now());

        return $diffInDays === 0 ? 'Active' : 'Inactive for '.$diffInDays.' days';
    }
}
