<?php

namespace App\Observers\Lab;

use App\Models\ComponentAssociation;

class ComponentAssociationObserver 
{
    /**
     * Handle the ComponentAssociation "created" event.
     *
     * @param  \App\Models\ComponentAssociation  $componentAssociation
     * @return void
     */
    public function created(ComponentAssociation $componentAssociation)
    {
        //
    }

    /**
     * Handle the ComponentAssociation "updated" event.
     *
     * @param  \App\Models\ComponentAssociation  $componentAssociation
     * @return void
     */
    public function updated(ComponentAssociation $componentAssociation)
    {
        //
    }

    /**
     * Handle the ComponentAssociation "deleted" event.
     *
     * @param  \App\Models\ComponentAssociation  $componentAssociation
     * @return void
     */
    public function deleted(ComponentAssociation $componentAssociation)
    {
        $getComponentAssociation = ComponentAssociation::select('id')->where('lab_id', $componentAssociation)->get()->toArray();
        $deleteComponentAssociation = ComponentAssociation::whereIn('id', $getComponentAssociation)->delete();
    }

    /**
     * Handle the ComponentAssociation "restored" event.
     *
     * @param  \App\Models\ComponentAssociation  $componentAssociation
     * @return void
     */
    public function restored(ComponentAssociation $componentAssociation)
    {
        //
    }

    /**
     * Handle the ComponentAssociation "force deleted" event.
     *
     * @param  \App\Models\ComponentAssociation  $componentAssociation
     * @return void
     */
    public function forceDeleted(ComponentAssociation $componentAssociation)
    {
        //
    }
}
