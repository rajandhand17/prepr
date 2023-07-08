<?php

namespace App\Observers\Lab;

use App\Models\ComponentAssociation;
use App\Models\Lab;
use App\Models\LabAcheivement;
use App\Models\LabAddress;
use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTagsGroups;

class LabObserver
{
    /**
     * Handle the Lab "created" event.
     *
     * @param  \App\Models\Lab  $lab
     * @return void
     */
    public function created(Lab $lab)
    {
        //
    }

    /**
     * Handle the Lab "updated" event.
     *
     * @param  \App\Models\Lab  $lab
     * @return void
     */
    public function updated(Lab $lab)
    {
        //
    }

    /**
     * Handle the Lab "deleted" event.
     *
     * @param  \App\Models\Lab  $lab
     * @return void
     */
    public function deleted(Lab $lab)
    {   
        LabAddress::where('lab_id',$lab->id)->delete();
        ComponentAssociation::where('lab_id', $lab->id)->delete();
        LabAcheivement::where('lab_id', $lab->id)->delete();
        LabExternalLinks::where('lab_id', $lab->id)->delete();
        LabTagsGroups::select('id')->where('lab_id',$lab->id)->delete();
        LabSkillsGroupsStack::select('id')->where('lab_id', $lab->id)->delete();
    }

    /**
     * Handle the Lab "restored" event.
     *
     * @param  \App\Models\Lab  $lab
     * @return void
     */
    public function restored(Lab $lab)
    {
        //
    }

    /**
     * Handle the Lab "force deleted" event.
     *
     * @param  \App\Models\Lab  $lab
     * @return void
     */
    public function forceDeleted(Lab $lab)
    {
        //
    }
}
