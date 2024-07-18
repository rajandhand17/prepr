<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            $this->mapApiRoutes();
            $this->mapWebRoutes();
            $this->mapMaestroAuthRoutes();
            $this->mapMaestroDashboardRoutes();
            $this->mapMaestroUsersRoutes();
            $this->mapMaestroOrganizationRoutes();
            $this->mapMaestroRoleAndPermissionRoutes();
            // $this->mapMaestroLabRoutes();
            $this->mapMaestroSkillRoutes();
            $this->mapMaestroCategoryRoutes();
            $this->mapMaestroProjectRoutes();
            $this->mapMaestroSponsorsRoutes();
            $this->mapMaestroTagRoutes();
            $this->mapMaestroSocialLinkRoutes();
            $this->mapMaestroResourceRoutes();
            $this->mapMaestroTrophyAwardsRoutes();
            $this->mapMaestroActivityAwardsRoutes();
            $this->mapMaestroChallengeRoutes();
            $this->mapMaestroMasterRoutes();
            $this->mapMaestroRankRoutes();
            $this->mapMaestroEmailTemplateRoutes();
            $this->mapMaestroExploreRoutes();
            $this->mapMaestroEmailLogsRoutes();
            $this->mapMaestroCloneLabRoutes();
            $this->mapMaestroLabRoutes();
            $this->mapMaestroPreBuiltAchievementRoutes();

            Route::prefix('api/v1/master/')->middleware('api')->group(base_path('routes/v1/master.php'));
            Route::prefix('api/v1/auth/')->middleware('api')->group(base_path('routes/v1/auth.php'));
            Route::prefix('api/v1/user/')->middleware('api')->group(base_path('routes/v1/user.php'));

            $this->mapProfileRoutes();
            $this->mapDiscussionsRoute();
            $this->mapExploreRoutes();
            $this->mapCareerRoutes();
            $this->mapSettingRoutes();
            $this->mapManageRoutes();
            $this->mapPublicRoutes();
            $this->mapChatRoutes();
            $this->mapProjectRoutes();
            $this->mapProjectMemberManagementRoutes();
            $this->mapDashboardRoutes();
            $this->mapTeamMatchingRoutes();
            $this->mapGO1Routes();
            $this->mapLeaderboardRoutes();
            $this->mapChannelApiRoutes();

            $this->mapStartPageRoutes();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
    }

    protected function mapProfileRoutes()
    {
        Route::prefix('api/v1/profile/')->middleware('api')->group(base_path('routes/v1/profile.php'));
    }

    protected function mapExploreRoutes()
    {
        Route::prefix('api/v1/explore/')->middleware('api')->group(base_path('routes/v1/explore.php'));
    }

    protected function mapCareerRoutes()
    {
        Route::prefix('api/v1/career/')->middleware('api')->group(base_path('routes/v1/career.php'));
    }

    protected function mapLeaderboardRoutes()
    {
        Route::prefix('api/v1/leaderboard/')->middleware('api')->group(base_path('routes/v1/leaderboard.php'));
    }

    public function mapDiscussionsRoute()
    {
        Route::prefix('api/v1/discussion/')->middleware('api')->group(base_path('routes/v1/discussion.php'));
    }

    protected function mapChatRoutes()
    {
        Route::prefix('api/v1/chat/conversation')->middleware('api')->group(base_path('routes/v1/chat/conversation.php'));
        Route::prefix('api/v1/chat/conversation')->middleware('api')->group(base_path('routes/v1/chat/message.php'));
    }

    protected function mapSettingRoutes()
    {
        Route::prefix('api/v1/setting/')->middleware('api')->group(base_path('routes/v1/setting.php'));
    }

    protected function mapStartPageRoutes()
    {
        Route::prefix('api/v1/start-page/')->middleware('api')->group(base_path('routes/v1/start-page.php'));
    }

    protected function mapProjectRoutes()
    {
        Route::prefix('api/v1/project/')->middleware('api')->group(base_path('routes/v1/project.php'));
    }

    protected function mapProjectMemberManagementRoutes()
    {
        Route::prefix('api/v1/member-management/project/')->middleware('api')->group(base_path('routes/v1/project-member-management.php'));
    }

    protected function mapManageRoutes()
    {
        Route::prefix('api/v1/manage/organization/')->middleware('api')->group(base_path('routes/v1/manage/organization.php'));
        Route::prefix('api/v1/manage/member-management/')->middleware('api')->group(base_path('routes/v1/manage/member-management.php'));
        Route::prefix('api/v1/manage/lab/')->middleware('api')->group(base_path('routes/v1/manage/lab.php'));
        Route::prefix('api/v1/manage/lab-program/')->middleware('api')->group(base_path('routes/v1/manage/lab-program.php'));
        Route::prefix('api/v1/manage/resource-module/')->middleware('api')->group(base_path('routes/v1/manage/resource-module.php'));
        Route::prefix('api/v1/manage/challenge/')->middleware('api')->group(base_path('routes/v1/manage/challenge.php'));
        Route::prefix('api/v1/manage/challenge-path/')->middleware('api')->group(base_path('routes/v1/manage/challenge-path.php'));
        Route::prefix('api/v1/manage/resource-collection/')->middleware('api')->group(base_path('routes/v1/manage/resource-collection.php'));
        Route::prefix('api/v1/manage/resource-group/')->middleware('api')->group(base_path('routes/v1/manage/resource-group.php'));
        Route::prefix('api/v1/manage/lab-marketplace/')->middleware('api')->group(base_path('routes/v1/manage/lab-marketplace.php'));
        Route::prefix('api/v1/manage/challenge-template/')->middleware('api')->group(base_path('routes/v1/manage/challenge-template.php'));
        Route::prefix('api/v1/manage/airmeet/')->middleware('api')->group(base_path('routes/v1/manage/airmeet.php'));
        Route::prefix('api/v1/manage/campus-connect/')->middleware('api')->group(base_path('routes/v1/campus-connect.php'));
        Route::prefix('api/v1/manage/unified/')->middleware('api')->group(base_path('routes/v1/manage/unified.php'));
    }

    protected function mapPublicRoutes()
    {
        Route::prefix('api/v1/public/scorm/')->middleware('api')->group(base_path('routes/v1/public/scorm.php'));
        Route::prefix('api/v1/public/organization/')->middleware('api')->group(base_path('routes/v1/public/organization.php'));
        Route::prefix('api/v1/public/lab/')->middleware('api')->group(base_path('routes/v1/public/lab.php'));
        Route::prefix('api/v1/public/invitation-management/')->middleware('api')->group(base_path('routes/v1/public/invitation-management.php'));
        Route::prefix('api/v1/public/lab-program/')->middleware('api')->group(base_path('routes/v1/public/lab-program.php'));
        Route::prefix('api/v1/public/challenge/')->middleware('api')->group(base_path('routes/v1/public/challenge.php'));
        Route::prefix('api/v1/public/resource-module/')->middleware('api')->group(base_path('routes/v1/public/resource-module.php'));
        Route::prefix('api/v1/public/challenge-path/')->middleware('api')->group(base_path('routes/v1/public/challenge-path.php'));
        Route::prefix('api/v1/public/resource-collection/')->middleware('api')->group(base_path('routes/v1/public/resource-collection.php'));
        Route::prefix('api/v1/public/resource-group/')->middleware('api')->group(base_path('routes/v1/public/resource-group.php'));
        Route::prefix('api/v1/public/achievement/')->middleware('api')->group(base_path('routes/v1/public/achievement.php'));
        Route::prefix('api/v1/public/skills/')->middleware('api')->group(base_path('routes/v1/public/skills.php'));
        Route::prefix('api/v1/public/advance-search/')->middleware('api')->group(base_path('routes/v1/public/advance-search.php'));
    }

    public function mapDashboardRoutes()
    {
        Route::prefix('api/v1/dashboard/organization')->middleware('api')->group(base_path('routes/v1/dashboard/organization.php'));
        Route::prefix('api/v1/dashboard/lab')->middleware('api')->group(base_path('routes/v1/dashboard/lab.php'));
        Route::prefix('api/v1/dashboard/user')->middleware('api')->group(base_path('routes/v1/dashboard/user.php'));
    }

    protected function mapTeamMatchingRoutes()
    {
        Route::prefix('api/v1/team-matching/')->middleware('api')->group(base_path('routes/v1/team-matching.php'));
    }

    public function mapGO1Routes()
    {
        Route::prefix('api/v1/go1')->middleware('api')->group(base_path('routes/v1/go1.php'));
    }

    public function mapChannelApiRoutes()
    {
        Route::prefix('api/v1/channel')->middleware('channel-api-auth')->group(base_path('routes/v1/manage/channel.php'));
    }

    public function mapMaestroDashboardRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/dashboard/dashboard.php'));
    }

    public function mapMaestroAuthRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/auth/auth.php'));
    }

    public function mapMaestroUsersRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/users/users.php'));
    }

    public function mapMaestroOrganizationRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/organization/organization.php'));
    }

    public function mapMaestroRoleAndPermissionRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/rolepermission/role-and-permission.php'));
    }

    //     public function mapMaestroLabRoutes()
    // {
    //     Route::prefix('maestro')->group(base_path('routes/maestro/lab/lab.php'));
    // }
    public function mapMaestroSkillRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/skill/skill.php'));
    }

    public function mapMaestroTagRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/tag/tag.php'));
    }

    public function mapMaestroCategoryRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/categories/categories.php'));
    }

    public function mapMaestroProjectRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/projects/projects.php'));
    }

    public function mapMaestroSponsorsRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/sponsors/sponsors.php'));
    }

    public function mapMaestroSocialLinkRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/sociallink/sociallink.php'));
    }

    public function mapMaestroResourceRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/resource/resource.php'));
    }

    public function mapMaestroTrophyAwardsRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/trophyawards/trophyawards.php'));
    }

    public function mapMaestroActivityAwardsRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/activityawards/activityawards.php'));
    }

    public function mapMaestroEmailTemplateRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/emailtemplates/emailtemplates.php'));
    }

    public function mapMaestroChallengeRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/challenge/challenge.php'));
    }

    public function mapMaestroMasterRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/master/master.php'));
    }

    public function mapMaestroRankRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/rank/rank.php'));
    }

    public function mapMaestroExploreRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/explore/explore.php'));
    }

    public function mapMaestroEmailLogsRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/emaillogs/emaillogs.php'));
    }

    public function mapMaestroCloneLabRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/clone-lab.php'));
    }

    public function mapMaestroLabRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/lab.php'));
    }

    public function mapMaestroPreBuiltAchievementRoutes()
    {
        Route::prefix('maestro')->group(base_path('routes/maestro/prebuiltachievement/prebuiltachievement.php'));
    }
}
