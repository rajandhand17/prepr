<?php

namespace Tests\Feature\Http\Controllers\Api\Master;

use App\Http\Controllers\Api\Master\MasterController;
use App\Repositories\Api\Master\MasterRepository;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class MasterControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Master\MasterController
 */
final class MasterControllerTest extends TestCase
{
    private MasterController $masterController;

    private MasterRepository|Mock $masterRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->masterRepository = Mockery::mock(MasterRepository::class);
        $this->masterController = new MasterController($this->masterRepository);
        $this->app->instance(MasterController::class, $this->masterController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->masterController);
        unset($this->masterRepository);
    }

    public function testGetCategories(): void
    {
        /** @todo This test is complete. */

        $response = $this->get('/api/v1/master/categories?language=en');
        /*With particular search name*/
        //$response = $this->get('/api/v1/master/categories?language=en&search=Co-working Space');
        /*French languages allowed*/
        //$response = $this->get('/api/v1/master/categories?language=fr');
        /*not exists langauges*/
        //$response = $this->get('/api/v1/master/categories?language=pu');
        /*without language parameter*/
        //$response = $this->get('/api/v1/master/categories');
        /**without language with search */
        //$response = $this->get('/api/v1/master/categories?search=Co-working Space');
        /**with language and search value blank*/
        //$response = $this->get('/api/v1/master/categories?language=en&search=');
        /**with language and search value null*/
        //$response = $this->get('/api/v1/master/categories?language=en&search=null');
        
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    public function testGetSkills(): void
    {
        /** @todo This test is complete. */
        
        $response = $this->get('/api/v1/master/skills?language=en');
            /*With particular search name*/
        //$response = $this->get('/api/v1/master/skills?language=en&search=Critical thinking');
        /*French languages allowed*/
        //$response = $this->get('/api/v1/master/skills?language=fr');
        /*not exists langauges*/
        //$response = $this->get('/api/v1/master/skills?language=pu');
        /*without language parameter*/
        //$response = $this->get('/api/v1/master/skills');
        /**without language with search */
        //$response = $this->get('/api/v1/master/skills?search=Critical thinking');
        /**with language and search value blank*/
        //$response = $this->get('/api/v1/master/skills?language=en&search=');
        /**with language and search value null*/
        //$response = $this->get('/api/v1/master/skills?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    public function testGetTags(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/tags?language=en');
        
            /*With particular search name*/
        //$response = $this->get('/api/v1/master/tags?language=en&search=No Poverty');
        /*French languages allowed*/
        //$response = $this->get('/api/v1/master/tags?language=fr');
        /*not exists langauges*/
        //$response = $this->get('/api/v1/master/tags?language=pu');
        /*without language parameter*/
        //$response = $this->get('/api/v1/master/tags');
        /**without language with search */
        //$response = $this->get('/api/v1/master/tags?search=No Poverty');
        /**with language and search value blank*/
        //$response = $this->get('/api/v1/master/tags?language=en&search=');
        /**with language and search value null*/
        //$response = $this->get('/api/v1/master/tags?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    public function testGetProjectIndustries(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/industries?language=en');
        
            /*With particular search name*/
        //$response = $this->get('/api/v1/master/industries?language=en&search=Automotive');
        /*French languages allowed*/
        //$response = $this->get('/api/v1/master/industries?language=fr');
        /*not exists langauges*/
        //$response = $this->get('/api/v1/master/industries?language=pu');
        /*without language parameter*/
        //$response = $this->get('/api/v1/master/industries');
        /**without language with search */
        //$response = $this->get('/api/v1/master/industries?search=Automotive');
        /**with language and search value blank*/
        //$response = $this->get('/api/v1/master/industries?language=en&search=');
        /**with language and search value null*/
        //$response = $this->get('/api/v1/master/industries?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    public function testGetProjectTypes(): void
    {
         /** @todo This test is complete. */
         $response = $this->get('/api/v1/master/types?language=en');
           /*With particular search name*/
        //$response = $this->get('/api/v1/master/types?language=en&search=Hackathon');
        /*French languages allowed*/
        //$response = $this->get('/api/v1/master/types?language=fr');
        /*not exists langauges*/
        //$response = $this->get('/api/v1/master/types?language=pu');
        /*without language parameter*/
        //$response = $this->get('/api/v1/master/types');
        /**without language with search */
        //$response = $this->get('/api/v1/master/types?search=Hackathon');
        /**with language and search value blank*/
        //$response = $this->get('/api/v1/master/types?language=en&search=');
        /**with language and search value null*/
        //$response = $this->get('/api/v1/master/types?language=en&search=null');
         $reponse=$response->json();
         if($reponse['success']==true){
             $response->assertOk();
         }else{
             $this->assertEquals($reponse['success'],false);
         }
    }

    public function testGetProjectStages(): void
    {
       /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/stages?language=en');
       /*With particular search name*/
    //$response = $this->get('/api/v1/master/stages?language=en&search=concept development');
    /*French languages allowed*/
    //$response = $this->get('/api/v1/master/stages?language=fr');
    /*not exists langauges*/
    //$response = $this->get('/api/v1/master/stages?language=pu');
    /*without language parameter*/
    //$response = $this->get('/api/v1/master/stages');
    /**without language with search */
    //$response = $this->get('/api/v1/master/stages?search=concept development');
    /**with language and search value blank*/
    //$response = $this->get('/api/v1/master/stages?language=en&search=');
    /**with language and search value null*/
    //$response = $this->get('/api/v1/master/stages?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetProjectVerticals(): void
    {
        /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/verticals?language=en');
        /*With particular search name*/
        //$response = $this->get('/api/v1/master/verticals?language=en&search=concept development');
        /*French languages allowed*/
        //$response = $this->get('/api/v1/master/verticals?language=fr');
        /*not exists langauges*/
        //$response = $this->get('/api/v1/master/verticals?language=pu');
        /*without language parameter*/
        //$response = $this->get('/api/v1/master/stages');
        /**without language with search */
        //$response = $this->get('/api/v1/master/stages?search=concept development');
        /**with language and search value blank*/
        //$response = $this->get('/api/v1/master/stages?language=en&search=');
        /**with language and search value null*/
        //$response = $this->get('/api/v1/master/stages?language=en&search=null');

       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetProjectStatus(): void
    {
         /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/status?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/status?language=en&search=Active');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/status?language=fr');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/status?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/status');
       /**without language with search */
       //$response = $this->get('/api/v1/master/status?search=Active');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/status?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/status?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetSocialLinks(): void
    {
          /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/links?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/links?language=en&search=facebook');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/links?language=fr');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/links?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/links');
       /**without language with search */
       //$response = $this->get('/api/v1/master/links?search=facebook');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/links?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/links?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetSkillGroups(): void
    {
        /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/skill-groups?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/skill-groups?language=en&search=facebook');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/skill-groups?language=fr');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/skill-groups?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/skill-groups');
       /**without language with search */
       //$response = $this->get('/api/v1/master/skill-groups?search=facebook');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/skill-groups?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/skill-groups?language=en&search=null');
        /**without language with skill stacks */
       //$response = $this->get('/api/v1/master/skill-groups?skill_stacks=facebook');
       /**with language and search value blank and skill stacks have value*/
       //$response = $this->get('/api/v1/master/skill-groups?language=en&search=&skill_stacks=skill_stacks');
       /**with language and search value null and skill stacks have value*/
       //$response = $this->get('/api/v1/master/skill-groups?language=en&search=null&skill_stacks=skill_stacks');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetRanks(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/ranks?language=en');
        
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/ranks?language=en&search=Rank 0');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/ranks?language=fr');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/ranks?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/ranks');
       /**without language with search */
       //$response = $this->get('/api/v1/master/ranks?search=Rank 0');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/ranks?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/ranks?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    public function testGetProjectSubmissionRequirements(): void
    {  
       /** @todo This test is complete. */
       
       $response = $this->get('/api/v1/master/project-submission-requirement?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=Complete project pitch');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/project-submission-requirement?language=fr-CA');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/project-submission-requirement?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/project-submission-requirement');
       /**without language with search */
       //$response = $this->get('/api/v1/master/project-submission-requirement?search=Complete project pitch');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetAchievementConditionLists(): void
    {
        /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/achievement-condition-list?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=Complete All');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/achievement-condition-list?language=fr-CA');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/achievement-condition-list?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/achievement-condition-list');
       /**without language with search */
       //$response = $this->get('/api/v1/master/achievement-condition-list?search=Complete All');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=null');
       
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetHosts(): void
    {
         /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/host?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/host?language=en&search=Google');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/host?language=fr-CA');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/host?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/host');
       /**without language with search */
       //$response = $this->get('/api/v1/master/host?search=Google');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/host?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/host?language=en&search=null');
       
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetFlexibleDateDurations(): void
    {
         /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/flexible-date-duration?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=Title');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/flexible-date-duration?language=fr-CA');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/flexible-date-duration?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/flexible-date-duration');
       /**without language with search */
       //$response = $this->get('/api/v1/master/flexible-date-duration?search=Title');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetPitchTemplates(): void
    {
         /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/pitch-templates?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/pitch-templates?language=en&search=PIE Framework');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/pitch-templates?language=fr-CA');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/pitch-templates?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/pitch-templates');
       /**without language with search */
       //$response = $this->get('/api/v1/master/pitch-templates?search=PIE Framework');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/pitch-templates?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/pitch-templates?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

    public function testGetLabConditions(): void
    {
         /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/lab-conditions?language=en');
       /*With particular search name*/
       //$response = $this->get('/api/v1/master/lab-conditions?language=en&search=title');
       /*French languages allowed*/
       //$response = $this->get('/api/v1/master/lab-conditions?language=fr-CA');
       /*not exists langauges*/
       //$response = $this->get('/api/v1/master/lab-conditions?language=pu');
       /*without language parameter*/
       //$response = $this->get('/api/v1/master/lab-conditions');
       /**without language with search */
       //$response = $this->get('/api/v1/master/lab-conditions?search=title');
       /**with language and search value blank*/
       //$response = $this->get('/api/v1/master/lab-conditions?language=en&search=');
       /**with language and search value null*/
       //$response = $this->get('/api/v1/master/lab-conditions?language=en&search=null');

       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
}
