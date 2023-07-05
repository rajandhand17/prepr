<?php

namespace App\Observers\Lab;

use App\Models\LabTagsGroups;

class LabTagsGroupsObserver
{
    /**
     * Handle the LabTagsGroups "created" event.
     *
     * @param  \App\Models\LabTagsGroups  $labTagsGroups
     * @return void
     */
    public function created(LabTagsGroups $labTagsGroups)
    {
        //
    }

    /**
     * Handle the LabTagsGroups "updated" event.
     *
     * @param  \App\Models\LabTagsGroups  $labTagsGroups
     * @return void
     */
    public function updated(LabTagsGroups $labTagsGroups)
    {
        //
    }

    /**
     * Handle the LabTagsGroups "deleted" event.
     *
     * @param  \App\Models\LabTagsGroups  $labTagsGroups
     * @return void
     */
    public function deleted(LabTagsGroups $labTagsGroups)
    {
        $labTagsGroups = LabTagsGroups::select('id')->where('lab_id', $labTagsGroups)->get()->toArray();
        if ($labTagsGroups) {
            $deleteLabTagsGroups = labTagsGroups::whereIn('id', $labTagsGroups)->delete();
            if (!$deleteLabTagsGroups) {
                return false;
            }
        }
    }

    /**
     * Handle the LabTagsGroups "restored" event.
     *
     * @param  \App\Models\LabTagsGroups  $labTagsGroups
     * @return void
     */
    public function restored(LabTagsGroups $labTagsGroups)
    {
        //
    }

    /**
     * Handle the LabTagsGroups "force deleted" event.
     *
     * @param  \App\Models\LabTagsGroups  $labTagsGroups
     * @return void
     */
    public function forceDeleted(LabTagsGroups $labTagsGroups)
    {
        //
    }
}
