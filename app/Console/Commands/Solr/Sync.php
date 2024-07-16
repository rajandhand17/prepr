<?php

namespace App\Console\Commands\Solr;

use App\Helpers\Solr\SolrChallengeHelper;
use App\Helpers\Solr\SolrChallengePathHelper;
use App\Helpers\Solr\SolrChallengeTemplateHelper;
use App\Helpers\Solr\SolrLabHelper;
use App\Helpers\Solr\SolrLabMarketPlaceHelper;
use App\Helpers\Solr\SolrLabProgramHelper;
use App\Helpers\Solr\SolrProjectHelper;
use App\Helpers\Solr\SolrResourceCollectionHelper;
use App\Helpers\Solr\SolrResourceGroupHelper;
use App\Helpers\Solr\SolrResourceModuleHelper;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data with apache solr';

    public function __construct(
        protected SolrLabHelper $solrLabHelper,
        protected SolrLabProgramHelper $solrLabProgramHelper,
        protected SolrChallengeHelper $solrChallengeHelper,
        protected SolrChallengeTemplateHelper $solrChallengeTemplateHelper,
        protected SolrChallengePathHelper $solrChallengePathHelper,
        protected SolrProjectHelper $solrProjectHelper,
        protected SolrResourceModuleHelper $solrResourceModuleHelper,
        protected SolrResourceCollectionHelper $solrResourceCollectionHelper,
        protected SolrResourceGroupHelper $solrResourceGroupHelper,
        protected SolrLabMarketPlaceHelper $solrLabMarketPlaceHelper
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Lab sync started.');
        $this->solrLabHelper->sync();
        $this->solrLabHelper->syncDeleted();
        $this->info('Lab sync completed.');

        $this->info('Lab program sync started.');
        $this->solrLabProgramHelper->sync();
        $this->solrLabProgramHelper->syncDeleted();
        $this->info('Lab program sync completed.');

        $this->info('Challenge sync started.');
        $this->solrChallengeHelper->sync();
        $this->solrChallengeHelper->syncDeleted();
        $this->info('Challenge sync completed.');

        $this->info('Challenge template sync started.');
        $this->solrChallengeTemplateHelper->sync();
        $this->solrChallengeTemplateHelper->syncDeleted();
        $this->info('Challenge template sync completed.');

        $this->info('Challenge path sync started.');
        $this->solrChallengePathHelper->sync();
        $this->solrChallengePathHelper->syncDeleted();
        $this->info('Challenge path sync completed.');

        $this->info('Project sync started.');
        $this->solrProjectHelper->sync();
        $this->solrProjectHelper->syncDeleted();
        $this->info('Project sync completed.');

        $this->info('Resource module sync started.');
        $this->solrResourceModuleHelper->sync();
        $this->solrResourceModuleHelper->syncDeleted();
        $this->info('Resource module sync completed.');

        $this->info('Resource collection sync started.');
        $this->solrResourceCollectionHelper->sync();
        $this->solrResourceCollectionHelper->syncDeleted();
        $this->info('Resource collection sync completed.');

        $this->info('Resource Group sync started.');
        $this->solrResourceGroupHelper->sync();
        $this->solrResourceGroupHelper->syncDeleted();
        $this->info('Resource Group sync completed.');

        $this->info('Lab Marketplace sync started.');
        $this->solrLabMarketPlaceHelper->sync();
        $this->solrLabMarketPlaceHelper->syncDeleted();
        $this->info('Lab Market place sync completed.');
    }
}
