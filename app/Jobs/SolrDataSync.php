<?php

namespace App\Jobs;

use App\Helpers\Solr\SolrBaseHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SolrDataSync
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $data, protected SolrBaseHelper $solrInstance, protected $type = 'update')
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->type === 'update') {
            $this->solrInstance->syncSingleton($this->data);
        }

        if ($this->type === 'delete') {
            $this->solrInstance->deleteSingleton($this->data->id);
        }
    }
}
