<?php

namespace App\Listeners\Lab;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Models\ComponentAssociation;
use App\Models\LabAcheivement;
use App\Models\LabAddress;
use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTagsGroups;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleDeleteLabAssociatedData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Labs\DeleteLabAssociatedData  $event
     * @return void
     */
    public function handle(DeleteLabAssociatedData $event)
    {   
        try {
            $lab_id=$event->labId;
            ComponentAssociation::where('lab_id', $lab_id)->delete();
            LabAcheivement::where('lab_id', $lab_id)->delete();
            LabExternalLinks::where('lab_id', $lab_id)->delete();
            LabExternalLinks::where('lab_id', $lab_id)->delete();
            LabTagsGroups::where('lab_id',$lab_id)->delete();
            LabSkillsGroupsStack::where('lab_id', $lab_id)->delete();
            LabAddress::where('lab_id', $lab_id)->delete();
            return true;
        } catch (\Throwable $th) {
          return false;
        }
    }
}
