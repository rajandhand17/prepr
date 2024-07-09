<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\ResourceGroup;

class ResourceGroupObserver
{
    /**
     * Handle the ResourceGroup "created" event.
     */
    public function created(ResourceGroup $resourceGroup): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceGroup::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceGroup, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceGroup "updated" event.
     */
    public function updated(ResourceGroup $resourceGroup): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceGroup::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceGroup, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceGroup "deleted" event.
     */
    public function deleted(ResourceGroup $resourceGroup): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceGroup::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceGroup, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the ResourceGroup "restored" event.
     */
    public function restored(ResourceGroup $resourceGroup): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceGroup::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceGroup, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceGroup "force deleted" event.
     */
    public function forceDeleted(ResourceGroup $resourceGroup): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceGroup::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceGroup, $solrInstance, 'delete'));
            }
        }
    }
}
