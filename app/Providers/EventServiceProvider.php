<?php

namespace App\Providers;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Events\ResourceModule\DeleteResourceModuleAssociatedData;
use App\Listeners\Lab\HandleDeleteLabAssociatedData;
use App\Listeners\ResourceModule\HandleDeleteResourceModuleAssociatedData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        DeleteResourceModuleAssociatedData::class=>[
            HandleDeleteResourceModuleAssociatedData::class,
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
}
