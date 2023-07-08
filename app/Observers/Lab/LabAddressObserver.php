<?php

namespace App\Observers\Lab;

use App\Models\LabAddress;

class LabAddressObserver
{
    /**
     * Handle the LabAddress "created" event.
     *
     * @param  \App\Models\LabAddress  $labAddress
     * @return void
     */
    public function created(LabAddress $labAddress)
    {
        //
    }

    /**
     * Handle the LabAddress "updated" event.
     *
     * @param  \App\Models\LabAddress  $labAddress
     * @return void
     */
    public function updated(LabAddress $labAddress)
    {
        //
    }

    /**
     * Handle the LabAddress "deleted" event.
     *
     * @param  \App\Models\LabAddress  $labAddress
     * @return void
     */
    public function deleted(LabAddress $labAddress)
    {
        $deleteLabaddress = LabAddress::where('lab_id', $labAddress)->delete();
        
    }

    /**
     * Handle the LabAddress "restored" event.
     *
     * @param  \App\Models\LabAddress  $labAddress
     * @return void
     */
    public function restored(LabAddress $labAddress)
    {
        //
    }

    /**
     * Handle the LabAddress "force deleted" event.
     *
     * @param  \App\Models\LabAddress  $labAddress
     * @return void
     */
    public function forceDeleted(LabAddress $labAddress)
    {
        //
    }
}
