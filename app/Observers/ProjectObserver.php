<?php

namespace App\Observers;

use App\Jobs\SolrDataSync;
use App\Models\Project;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Project::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($project, $solrInstance));
            }
        }
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Project::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($project, $solrInstance));
            }
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Project::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($project, $solrInstance, 'delete'));
            }
        }
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Project::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($project, $solrInstance));
            }
        }
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        if (!app()->runningInConsole()) { // if the application is not running via console
            $solrInstance = Project::query()->getSolrInstance();
            if ($solrInstance) {
                dispatch(new SolrDataSync($project, $solrInstance, 'delete'));
            }
        }
    }
}
