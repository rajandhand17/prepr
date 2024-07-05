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

class SolrMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate solr schema';

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
    public function handle(): void
    {
        $this->solrLabHelper->migrateTextField();
        $this->info('Text ngram field migrated.');
        $this->solrLabHelper->migrate();
        $this->info('Lab schema migrated.');
        $this->solrLabProgramHelper->migrate();
        $this->info('Lab program schema migrated.');
        $this->solrChallengeHelper->migrate();
        $this->info('Challenge schema migrated.');
        $this->solrChallengePathHelper->migrate();
        $this->info('Challenge path schema migrated.');
        $this->solrChallengeTemplateHelper->migrate();
        $this->info('Challenge Template schema has been migrated.');
        $this->solrProjectHelper->migrate();
        $this->info('Project schema has been migrated.');
        $this->solrResourceModuleHelper->migrate();
        $this->info('Resource module schema has been migrated.');
        $this->solrResourceCollectionHelper->migrate();
        $this->info('Resource collection schema has been migrated.');
        $this->solrResourceGroupHelper->migrate();
        $this->info('Resource group schema has been migrated.');
        $this->solrLabMarketPlaceHelper->migrate();
        $this->info('Lab Marketplace schema has been migrated.');
    }
}
