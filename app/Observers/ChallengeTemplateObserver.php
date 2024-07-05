<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\ChallengeTemplate;

class ChallengeTemplateObserver
{
    /**
     * Handle the ChallengeTemplate "created" event.
     */
    public function created(ChallengeTemplate $challengePathTemplate): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengeTemplate::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePathTemplate, $solrInstance));
            }
        }
    }

    /**
     * Handle the ChallengeTemplate "updated" event.
     */
    public function updated(ChallengeTemplate $challengePathTemplate): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengeTemplate::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePathTemplate, $solrInstance));
            }
        }
    }

    /**
     * Handle the ChallengeTemplate "deleted" event.
     */
    public function deleted(ChallengeTemplate $challengePathTemplate): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengeTemplate::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePathTemplate, $solrInstance, 'deleted'));
            }
        }
    }

    /**
     * Handle the ChallengeTemplate "restored" event.
     */
    public function restored(ChallengeTemplate $challengePathTemplate): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengeTemplate::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePathTemplate, $solrInstance));
            }
        }
    }

    /**
     * Handle the ChallengeTemplate "force deleted" event.
     */
    public function forceDeleted(ChallengeTemplate $challengePathTemplate): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = ChallengeTemplate::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($challengePathTemplate, $solrInstance, 'deleted'));
            }
        }
    }
}
