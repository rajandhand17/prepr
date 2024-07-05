<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\ChallengePath;

class ChallengePathObserver
{
    /**
     * Handle the ChallengePath "created" event.
     */
    public function created(ChallengePath $challengePath): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengePath::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePath, $solrInstance));
            }
        }
    }

    /**
     * Handle the ChallengePath "updated" event.
     */
    public function updated(ChallengePath $challengePath): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengePath::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePath, $solrInstance));
            }
        }
    }

    /**
     * Handle the ChallengePath "deleted" event.
     */
    public function deleted(ChallengePath $challengePath): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengePath::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePath, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the ChallengePath "restored" event.
     */
    public function restored(ChallengePath $challengePath): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengePath::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePath, $solrInstance));
            }
        }
    }

    /**
     * Handle the ChallengePath "force deleted" event.
     */
    public function forceDeleted(ChallengePath $challengePath): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengePath::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePath, $solrInstance, 'delete'));
            }
        }
    }
}
