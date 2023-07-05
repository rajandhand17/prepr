<?php

namespace App\Observers\Lab;

use App\Models\LabSkillsGroupsStack;

class LabSkillsGroupsStackObserver
{
    /**
     * Handle the LabSkillsGroupsStack "created" event.
     *
     * @param  \App\Models\LabSkillsGroupsStack  $labSkillsGroupsStack
     * @return void
     */
    public function created(LabSkillsGroupsStack $labSkillsGroupsStack)
    {
        //
    }

    /**
     * Handle the LabSkillsGroupsStack "updated" event.
     *
     * @param  \App\Models\LabSkillsGroupsStack  $labSkillsGroupsStack
     * @return void
     */
    public function updated(LabSkillsGroupsStack $labSkillsGroupsStack)
    {
        //
    }

    /**
     * Handle the LabSkillsGroupsStack "deleted" event.
     *
     * @param  \App\Models\LabSkillsGroupsStack  $labSkillsGroupsStack
     * @return void
     */
    public function deleted(LabSkillsGroupsStack $labSkillsGroupsStack)
    {
        $checkExistsLabSkillsGroupsStack = LabSkillsGroupsStack::select('id')->where('lab_id', $lab_id)->get()->toArray();
        if ($checkExistsLabSkillsGroupsStack) {
            $deleteLabSkillsGroupsStack = LabSkillsGroupsStack::whereIn('id', $checkExistsLabSkillsGroupsStack)->delete();
            if (!$deleteLabSkillsGroupsStack) {
                return false;
            }
        }
    }

    /**
     * Handle the LabSkillsGroupsStack "restored" event.
     *
     * @param  \App\Models\LabSkillsGroupsStack  $labSkillsGroupsStack
     * @return void
     */
    public function restored(LabSkillsGroupsStack $labSkillsGroupsStack)
    {
        //
    }

    /**
     * Handle the LabSkillsGroupsStack "force deleted" event.
     *
     * @param  \App\Models\LabSkillsGroupsStack  $labSkillsGroupsStack
     * @return void
     */
    public function forceDeleted(LabSkillsGroupsStack $labSkillsGroupsStack)
    {
        //
    }
}
