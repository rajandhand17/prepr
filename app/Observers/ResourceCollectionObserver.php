<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\ResourceCollection;

class ResourceCollectionObserver
{
    /**
     * Handle the ResourceCollection "created" event.
     */
    public function created(ResourceCollection $resourceCollection): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceCollection::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceCollection, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceCollection "updated" event.
     */
    public function updated(ResourceCollection $resourceCollection): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceCollection::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceCollection, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceCollection "deleted" event.
     */
    public function deleted(ResourceCollection $resourceCollection): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceCollection::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceCollection, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the ResourceCollection "restored" event.
     */
    public function restored(ResourceCollection $resourceCollection): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceCollection::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceCollection, $solrInstance));
            }
        }
    }

    /**
     * Handle the ResourceCollection "force deleted" event.
     */
    public function forceDeleted(ResourceCollection $resourceCollection): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ResourceCollection::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($resourceCollection, $solrInstance, 'delete'));
            }
        }
    }
}
