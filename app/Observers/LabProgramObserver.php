<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\LabProgram;

class LabProgramObserver
{
    /**
     * Handle the LabProgram "created" event.
     */
    public function created(LabProgram $labProgram): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabProgram::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labProgram, $solrInstance));
            }
        }
    }

    /**
     * Handle the LabProgram "updated" event.
     */
    public function updated(LabProgram $labProgram): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabProgram::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labProgram, $solrInstance));
            }
        }
    }

    /**
     * Handle the LabProgram "deleted" event.
     */
    public function deleted(LabProgram $labProgram): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabProgram::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labProgram, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the LabProgram "restored" event.
     */
    public function restored(LabProgram $labProgram): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabProgram::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labProgram, $solrInstance));
            }
        }
    }

    /**
     * Handle the LabProgram "force deleted" event.
     */
    public function forceDeleted(LabProgram $labProgram): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabProgram::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labProgram, $solrInstance, 'delete'));
            }
        }
    }
}
