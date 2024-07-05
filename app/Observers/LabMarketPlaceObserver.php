<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\LabMarketPlace;

class LabMarketPlaceObserver
{
    /**
     * Handle the LabMarketPlace "created" event.
     */
    public function created(LabMarketPlace $labMarketPlace): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabMarketPlace::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labMarketPlace, $solrInstance));
            }
        }
    }

    /**
     * Handle the LabMarketPlace "updated" event.
     */
    public function updated(LabMarketPlace $labMarketPlace): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabMarketPlace::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labMarketPlace, $solrInstance));
            }
        }
    }

    /**
     * Handle the LabMarketPlace "deleted" event.
     */
    public function deleted(LabMarketPlace $labMarketPlace): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabMarketPlace::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labMarketPlace, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the LabMarketPlace "restored" event.
     */
    public function restored(LabMarketPlace $labMarketPlace): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabMarketPlace::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labMarketPlace, $solrInstance));
            }
        }
    }

    /**
     * Handle the LabMarketPlace "force deleted" event.
     */
    public function forceDeleted(LabMarketPlace $labMarketPlace): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = LabMarketPlace::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($labMarketPlace, $solrInstance, 'delete'));
            }
        }
    }
}
