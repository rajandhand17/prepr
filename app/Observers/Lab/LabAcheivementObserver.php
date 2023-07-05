<?php

namespace App\Observers\Lab;

use App\Models\LabAcheivement;

class LabAcheivementObserver
{
    /**
     * Handle the LabAcheivement "created" event.
     *
     * @param  \App\Models\LabAcheivement  $labAcheivement
     * @return void
     */
    public function created(LabAcheivement $labAcheivement)
    {
        //
    }

    /**
     * Handle the LabAcheivement "updated" event.
     *
     * @param  \App\Models\LabAcheivement  $labAcheivement
     * @return void
     */
    public function updated(LabAcheivement $labAcheivement)
    {
        //
    }

    /**
     * Handle the LabAcheivement "deleted" event.
     *
     * @param  \App\Models\LabAcheivement  $labAcheivement
     * @return void
     */
    public function deleted(LabAcheivement $labAcheivement)
    {
        
        $deleteLabAchievement = LabAcheivement::where('lab_id', $labAcheivement)->delete();
        if (!$deleteLabAchievement) {
            return false;
        }
    }

    /**
     * Handle the LabAcheivement "restored" event.
     *
     * @param  \App\Models\LabAcheivement  $labAcheivement
     * @return void
     */
    public function restored(LabAcheivement $labAcheivement)
    {
        //
    }

    /**
     * Handle the LabAcheivement "force deleted" event.
     *
     * @param  \App\Models\LabAcheivement  $labAcheivement
     * @return void
     */
    public function forceDeleted(LabAcheivement $labAcheivement)
    {
        //
    }
}
