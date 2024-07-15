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
use App\Listeners\Organization\HandleDeleteOrganizationAssociatedData;
use App\Listeners\Project\HandleDeleteProjectAssociatedData;
use App\Listeners\ResourceCollection\HandleDeleteResourceCollectionAssociatedData;
use App\Listeners\ResourceGroup\HandleDeleteResourceGroupAssociatedData;
use App\Listeners\ResourceModule\HandleDeleteResourceModuleAssociatedData;
use App\Models\Lab;
use App\Observers\LabObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;
use App\Listeners\LogSentEmail;

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
        Lab::observe(LabObserver::class);
    }
}
