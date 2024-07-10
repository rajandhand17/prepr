<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\ResourceModule;

class ResourceModuleObserver
{
    /**
     * Handle the ResourceModule "created" event.
     */
    public function created(ResourceModule $resourceModule): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceModule::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceModule, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceModule "updated" event.
     */
    public function updated(ResourceModule $resourceModule): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceModule::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceModule, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceModule "deleted" event.
     */
    public function deleted(ResourceModule $resourceModule): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceModule::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceModule, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the ResourceModule "restored" event.
     */
    public function restored(ResourceModule $resourceModule): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceModule::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceModule, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceModule "force deleted" event.
     */
    public function forceDeleted(ResourceModule $resourceModule): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceModule::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceModule, $solrInstance, 'delete'));
            }
        }
    }
}
