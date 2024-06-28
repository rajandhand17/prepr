<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        L5Swagger\L5SwaggerServiceProvider::class,
        \PhpUnitGen\Console\Adapters\Laravel\PhpUnitGenServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        Yajra\DataTables\DataTablesServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,

        /*
         * API Service Providers
         */

        App\Repositories\Api\Master\MasterServiceProvider::class,
        App\Repositories\Api\Auth\AuthServiceProvider::class,
        App\Repositories\Api\Discussion\DiscussionServiceProvider::class,
        App\Repositories\Api\Project\ProjectServiceProvider::class,
        App\Repositories\Api\ProjectMemberManagement\ProjectMemberManagementServiceProvider::class,
        App\Repositories\Api\Chat\Conversation\ConversationServiceProvider::class,
        App\Repositories\Api\Chat\Message\MessageServiceProvider::class,
        /* Scorm */
        App\Repositories\Api\Manage\Scorm\ScormServiceProvider::class,

        /* Manage */
        App\Repositories\Api\Manage\Organization\OrganizationServiceProvider::class,
        App\Repositories\Api\Manage\MemberManagement\MemberManagementServiceProvider::class,
        App\Repositories\Api\Manage\Lab\LabServiceProvider::class,
        App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceServiceProvider::class,
        App\Repositories\Api\Manage\LabProgram\LabProgramServiceProvider::class,
        App\Repositories\Api\Manage\ResourceModule\ResourceModuleServiceProvider::class,
        App\Repositories\Api\Manage\Challenge\ChallengeServiceProvider::class,
        App\Repositories\Api\Manage\ChallengePath\ChallengePathServiceProvider::class,
        App\Repositories\Api\Manage\ResourceCollection\ResourceCollectionServiceProvider::class,
        App\Repositories\Api\Manage\ResourceGroup\ResourceGroupServiceProvider::class,
        App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateServiceProvider::class,
        App\Repositories\Api\Manage\AirmeetEvent\AirmeetEventServiceProvider::class,
        App\Repositories\Api\Manage\UnifiedConnection\UnifiedConnectionServiceProvider::class,

        /* Public */
        App\Repositories\Api\Public\Organization\OrganizationServiceProvider::class,
        App\Repositories\Api\Public\Lab\LabServiceProvider::class,
        App\Repositories\Api\Public\LabProgram\LabProgramServiceProvider::class,
        App\Repositories\Api\Public\InvitationManagement\InvitationManagementServiceProvider::class,
        App\Repositories\Api\Public\ResourceModule\ResourceModuleServiceProvider::class,
        App\Repositories\Api\Public\Challenge\ChallengeServiceProvider::class,
        App\Repositories\Api\Public\ChallengePath\ChallengePathServiceProvider::class,
        App\Repositories\Api\Public\ResourceCollection\ResourceCollectionServiceProvider::class,
        App\Repositories\Api\Public\ResourceGroup\ResourceGroupServiceProvider::class,
        App\Repositories\Api\Public\Achievement\AchievementServiceProvider::class,
        App\Repositories\Api\Setting\SettingServiceProvider::class,
        App\Repositories\Api\Public\Skill\SkillServiceProvider::class,
        App\Repositories\Api\Public\Scorm\ScormServiceProvider::class,
        App\Repositories\Api\Public\Scorm\ScormTracking\ScormTrackingServiceProvider::class,
        App\Repositories\Api\Public\AirmeetEvent\AirmeetEventServiceProvider::class,

        /* GO1 */
        App\Repositories\Api\GO1\GO1ServiceProvider::class,

        /* Campus Connect */
        App\Repositories\Api\Manage\CampusConnect\CampusConnectServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'Image'      => Intervention\Image\Facades\Image::class,
        'PDF'        => Barryvdh\DomPDF\Facade::class,
        'DataTables' => Yajra\DataTables\Facades\DataTables::class,

    ])->toArray(),

];
