<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
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
        'eloquent.deleted: App\Models\Lab' => [
            'App\Observers\Lab\LabObserver',
            'App\Observers\Lab\LabAcheivementObserver@deleted',
            'App\Observers\Lab\LabAddressObserver@deleted',
            'App\Observers\Lab\LabExternalLinksObserver@deleted',
            'App\Observers\Lab\LabObserver@deleted',
            'App\Observers\Lab\LabSkillsGroupsStackObserver@deleted',
            'App\Observers\Lab\LabTagsGroupsObserver@deleted',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
    }

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
