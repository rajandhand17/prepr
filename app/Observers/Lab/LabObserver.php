<?php

namespace App\Observers\Lab;

use App\Models\Lab;

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
        $lab=Lab::where('id', $lab)->delete();
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
