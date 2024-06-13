<?php

namespace App\Observers;

use App\Jobs\MagnetWebhook;
use App\Models\Lab;

class LabObserver
{
    /**
     * Handle the Lab "created" event.
     */
    public function created(Lab $lab)
    {
        dispatch(new MagnetWebhook($lab, 'new'));
    }

    /**
     * Handle the Lab "updated" event.
     */
    public function updated(Lab $lab): void
    {
        dispatch(new MagnetWebhook($lab, 'updated'));
    }

    /**
     * Handle the Lab "deleted" event.
     */
    public function deleted(Lab $lab): void
    {
        dispatch(new MagnetWebhook($lab, 'deleted'));
    }

    /**
     * Handle the Lab "restored" event.
     */
    public function restored(Lab $lab): void
    {
        //
    }

    /**
     * Handle the Lab "force deleted" event.
     */
    public function forceDeleted(Lab $lab): void
    {
        //
    }
}
