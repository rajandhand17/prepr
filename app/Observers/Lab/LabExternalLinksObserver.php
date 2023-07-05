<?php

namespace App\Observers\Lab;

use App\Models\LabExternalLinks;

class LabExternalLinksObserver
{
    /**
     * Handle the LabExternalLinks "created" event.
     *
     * @param  \App\Models\LabExternalLinks  $labExternalLinks
     * @return void
     */
    public function created(LabExternalLinks $labExternalLinks)
    {
        //
    }

    /**
     * Handle the LabExternalLinks "updated" event.
     *
     * @param  \App\Models\LabExternalLinks  $labExternalLinks
     * @return void
     */
    public function updated(LabExternalLinks $labExternalLinks)
    {
        //
    }

    /**
     * Handle the LabExternalLinks "deleted" event.
     *
     * @param  \App\Models\LabExternalLinks  $labExternalLinks
     * @return void
     */
    public function deleted(LabExternalLinks $labExternalLinks)
    {
        $checkExists = LabExternalLinks::select('id')->where('lab_id', $labExternalLinks)->get()->toArray();
        if($checkExists) {
            $deleteLabExternalLinks = LabExternalLinks::whereIn('id', $checkExists)->delete();
            if (!$deleteLabExternalLinks) {
                return false;
            }
        }
    }

    /**
     * Handle the LabExternalLinks "restored" event.
     *
     * @param  \App\Models\LabExternalLinks  $labExternalLinks
     * @return void
     */
    public function restored(LabExternalLinks $labExternalLinks)
    {
        //
    }

    /**
     * Handle the LabExternalLinks "force deleted" event.
     *
     * @param  \App\Models\LabExternalLinks  $labExternalLinks
     * @return void
     */
    public function forceDeleted(LabExternalLinks $labExternalLinks)
    {
        //
    }
}
