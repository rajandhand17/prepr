<?php

namespace App\Providers;

use App\Events\ChallengePath\DeleteChallengePathAssociatedData;
use App\Events\ChallengeTemplate\DeleteChallengeTemplateAssociatedData;
use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Events\Labs\DeleteLabAssociatedData;
use App\Events\Organization\DeleteOrganizationAssociatedData;
use App\Events\Project\DeleteProjectAssociatedData;
use App\Events\ResourceCollection\DeleteResourceCollectionAssociatedData;
use App\Events\ResourceGroup\DeleteResourceGroupAssociatedData;
use App\Events\ResourceModule\DeleteResourceModuleAssociatedData;
use App\Listeners\ChallengePath\HandleDeleteChallengePathAssociatedData;
use App\Listeners\ChallengeTemplate\HandleDeleteChallengeTemplateAssociatedData;
use App\Listeners\Lab\HandleDeleteLabAssociatedData;
use App\Listeners\LabMarketplace\HandleDeleteLabMarketplaceAssociatedData;
use App\Listeners\LogSentEmail;
use App\Listeners\Organization\HandleDeleteOrganizationAssociatedData;
use App\Listeners\Project\HandleDeleteProjectAssociatedData;
use App\Listeners\ResourceCollection\HandleDeleteResourceCollectionAssociatedData;
use App\Listeners\ResourceGroup\HandleDeleteResourceGroupAssociatedData;
use App\Listeners\ResourceModule\HandleDeleteResourceModuleAssociatedData;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChallengeTemplate;
use App\Models\Lab;
use App\Models\LabMarketplace;
use App\Models\LabProgram;
use App\Models\Project;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use App\Observers\ChallengeObserver;
use App\Observers\ChallengePathObserver;
use App\Observers\ChallengeTemplateObserver;
use App\Observers\LabMarketPlaceObserver;
use App\Observers\LabObserver;
use App\Observers\LabProgramObserver;
use App\Observers\ProjectObserver;
use App\Observers\ResourceCollectionObserver;
use App\Observers\ResourceGroupObserver;
use App\Observers\ResourceModuleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DeleteLabAssociatedData::class => [
            HandleDeleteLabAssociatedData::class,
        ],
        DeleteResourceModuleAssociatedData::class => [
            HandleDeleteResourceModuleAssociatedData::class,
        ],
        DeleteChallengePathAssociatedData::class => [
            HandleDeleteChallengePathAssociatedData::class,
        ],
        DeleteResourceCollectionAssociatedData::class => [
            HandleDeleteResourceCollectionAssociatedData::class,
        ],
        DeleteResourceGroupAssociatedData::class => [
            HandleDeleteResourceGroupAssociatedData::class,
        ],
        DeleteLabMarketplaceAssociatedData::class => [
            HandleDeleteLabMarketplaceAssociatedData::class,
        ],
        DeleteChallengeTemplateAssociatedData::class => [
            HandleDeleteChallengeTemplateAssociatedData::class,
        ],
        DeleteProjectAssociatedData::class => [
            HandleDeleteProjectAssociatedData::class,
        ],
        DeleteOrganizationAssociatedData::class => [
            HandleDeleteOrganizationAssociatedData::class,
        ],
        MessageSent::class => [
            LogSentEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }

    public function boot(): void
    {
        Challenge::observe(ChallengeObserver::class);
        ChallengePath::observe(ChallengePathObserver::class);
        ChallengeTemplate::observe(ChallengeTemplateObserver::class);
        Lab::observe(LabObserver::class);
        LabMarketplace::observe(LabMarketPlaceObserver::class);
        LabProgram::observe(LabProgramObserver::class);
        Project::observe(ProjectObserver::class);
        ResourceGroup::observe(ResourceGroupObserver::class);
        ResourceCollection::observe(ResourceCollectionObserver::class);
        ResourceModule::observe(ResourceModuleObserver::class);
    }
}
