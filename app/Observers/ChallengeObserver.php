<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\Challenge;

class ChallengeObserver
{
    /**
     * Handle the Challenge "created" event.
     */
    public function created(Challenge $challenge): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Challenge::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challenge, $solrInstance));
            }
        }
    }

    /**
     * Handle the Challenge "updated" event.
     */
    public function updated(Challenge $challenge): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Challenge::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challenge, $solrInstance));
            }
        }
    }

    /**
     * Handle the Challenge "deleted" event.
     */
    public function deleted(Challenge $challenge): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Challenge::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challenge, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the Challenge "restored" event.
     */
    public function restored(Challenge $challenge): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Challenge::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challenge, $solrInstance));
            }
        }
    }

    /**
     * Handle the Challenge "force deleted" event.
     */
    public function forceDeleted(Challenge $challenge): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Challenge::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challenge, $solrInstance, 'delete'));
            }
        }
    }
}
