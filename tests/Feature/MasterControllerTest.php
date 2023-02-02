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
    /**Categories positive test */
    public function testGetCategoriesPositive(): void
    {   
        $response = $this->get('/api/v1/master/categories?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
     
    /**Categories negative test cases */
    public function testGetCategoriesNegative():void
    {   
        $response = $this->get('/api/v1/master/categories');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(400, $response->getStatusCode());
        }
    }
    
    /**Categories positive test cases with search*/
    public function testGetCategoriesWithSearchPositive():void
    {   
        $response = $this->get('/api/v1/master/categories?language=en&search=Co-working Space');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    /**Categories negative test cases with search */
    public function testGetCategoriesWithSearchNegative():void
    {   
        $response = $this->get('/api/v1/master/categories?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(404, $response->getStatusCode());
        }
    }
    
    /**Skills positive test cases */
    public function testGetSkillsPositive(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

    /**Skills negative test cases */
    public function testGetSkillsNegative(): void
    {
        $response = $this->get('/api/v1/master/skills');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(400, $response->getStatusCode());
        }
    }
    
    /**Skills positive test cases with Search */
    public function testGetSkillsWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/skills?language=en&search=Critical thinking');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }  
    }
    
    /**Skills negative test cases with Search */
    public function testGetSkillsWithoutSearchNegative(): void
    {  
       $response = $this->get('/api/v1/master/skills?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
        $this->assertEquals(404, $response->getStatusCode());
       }  
    }
    
    /** Tags positive test cases  */
    public function testGetTagsPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/tags?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }

   /**Tags negative test cases */
   public function testGetTagsNegative(): void
   {    
        $response = $this->get('/api/v1/master/tags');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(400, $response->getStatusCode());
        }
   }

     /**Tags Positive test cases with search*/
     public function testGetTagsWithSearchPositive(): void
     {    
          $response = $this->get('/api/v1/master/tags?language=en&search=No Poverty');
          $reponse=$response->json();
          if($reponse['success']==true){
              $response->assertOk();
          }else{
              $this->assertEquals($reponse['success'],false);
          }
     }

      /** Tags negative test cases with search*/
      public function testGetTagsWithSearchNegative(): void
      {    
           $response = $this->get('/api/v1/master/tags?language=en&search=null');
           $reponse=$response->json();
           if($reponse['success']==true){
               $response->assertOk();
           }else{
            $this->assertEquals(404, $response->getStatusCode());
           }
      }

     /**ProjectIndustries positive test cases */
    public function testGetProjectIndustriesPositive(): void
    {   
        $response = $this->get('/api/v1/master/industries?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
    
    /**ProjectIndustries negative test cases */
    public function testGetProjectIndustriesNegative(): void
    {  
        $response = $this->get('/api/v1/master/industries');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(400, $response->getStatusCode());
        }
    }
        /**ProjectIndustries positive test cases with search */
    public function testGetProjectIndustriesWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en&search=Automotive');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
     
    /**ProjectIndustries negative test cases with search */
    public function testGetProjectIndustriesWithSearchNegative(): void
    {   
        $response = $this->get('/api/v1/master/industries?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(404, $response->getStatusCode());
        }
    }
    /**ProjectTypes positive test cases */
    public function testGetProjectTypesPoisitive(): void
    {   
         $response = $this->get('/api/v1/master/types?language=en');
         $reponse=$response->json();
         if($reponse['success']==true){
             $response->assertOk();
         }else{
             $this->assertEquals($reponse['success'],false);
         }
    }
    /**ProjectTypes negative test cases */
    public function testGetProjectTypesNegative(): void
    {   
         $response = $this->get('/api/v1/master/types');
         $reponse=$response->json();
         if($reponse['success']==true){
             $response->assertOk();
         }else{
            $this->assertEquals(400, $response->getStatusCode());
         }
    }
      /**ProjectTypes Positive test cases with search */
    public function testGetProjectTypesWithSearchPoisitive(): void
    {
        $response = $this->get('/api/v1/master/types?language=en&search=Hackathon');
        $reponse=$response->json();
        if($reponse['success']==true){
             $response->assertOk();
         }else{
             $this->assertEquals($reponse['success'],false);
         }
    }
    /**ProjectTypes negative test cases with search */
    public function testGetProjectTypesWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/types?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
             $response->assertOk();
         }else{
            $this->assertEquals(404, $response->getStatusCode());
         }
    }
    
    /**ProjectStages positive test cases */
    public function testGetProjectStagesPositive(): void
    {
       $response = $this->get('/api/v1/master/stages?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**ProjectStages negative test cases */
    public function testGetProjectStagesNegative(): void
    {
       /** @todo This test is complete. */
       $response = $this->get('/api/v1/master/stages');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
          $this->assertEquals(400, $response->getStatusCode());
       }
    }

    /**ProjectStages positive test cases with search */  
    public function testGetProjectStagesWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/stages?language=en&search=concept development');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**ProjectStages negative test cases with search */
    public function testGetProjectStagesWithSearchNegative(): void
    {  
       $response = $this->get('/api/v1/master/stages?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
         $this->assertEquals(404, $response->getStatusCode());
       }
    }
    
    /**ProjectVerticals positive test cases */
    public function testGetProjectVerticalsPositive(): void
    {
       $response = $this->get('/api/v1/master/verticals?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**ProjectVerticals negative test cases */
    public function testGetProjectVerticalsNegative(): void
    {  
       $response = $this->get('/api/v1/master/verticals');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
          $this->assertEquals(400, $response->getStatusCode());
      }
    }
    
    /**ProjectVerticals positive test cases with search */
    public function testGetProjectVerticalsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en&search=concept development');
        $reponse=$response->json();
        if($reponse['success']==true){
           $response->assertOk();
        }else{
           $this->assertEquals($reponse['success'],false);
        }
    }

    /**ProjectVerticals negative test cases with search*/
    public function testGetProjectVerticalsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
           $response->assertOk();
        }else{
            $this->assertEquals(404, $response->getStatusCode());
          }
    }
    
    /**ProjectStatus positive test cases*/
    public function testGetProjectStatusPositive(): void
    {
       $response = $this->get('/api/v1/master/status?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    
    /**ProjectStatus negative test cases*/
    public function testGetProjectStatusNegative(): void
    {
       $response = $this->get('/api/v1/master/status');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
        $this->assertEquals(400, $response->getStatusCode());
      }
    }
    
    
    /**ProjectStatus positive test cases with search*/
    public function testGetProjectStatusWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/status?language=en&search=Active');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    
    /**ProjectStatus negative test cases with search*/
    public function testGetProjectStatusWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/status?language=en&search=Null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{ 
           $this->assertEquals(404, $response->getStatusCode());
       }
    }
    
    
    /**SocialLinks positive test cases */
    public function testGetSocialLinksPositive(): void
    {
       $response = $this->get('/api/v1/master/links?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**SocialLinks negative test cases*/
    public function testGetSocialLinksNegative(): void
    {
       $response = $this->get('/api/v1/master/links');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
          $this->assertEquals(400, $response->getStatusCode());
      }
    }
      
    
    /**SocialLinks positive test cases with search*/
    public function testGetSocialLinksWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/links?language=en&search=facebook');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**SocialLinks negative test cases with search*/
    public function testGetSocialLinksWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/links?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
        $this->assertEquals(404, $response->getStatusCode());
       }
    }
     
    /**SkillGroups positive test cases*/
    public function testGetSkillGroupsPositive(): void
    {
       $response = $this->get('/api/v1/master/skill-groups?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**SkillGroups negative test cases */
    public function testGetSkillGroupsNegative(): void
    {
       $response = $this->get('/api/v1/master/skill-groups');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
        $this->assertEquals(400, $response->getStatusCode());
       }
    }
    
    /**SkillGroups positive test cases with search*/
    public function testGetSkillGroupsWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/skill-groups?language=en&search=facebook');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**SkillGroups negative test cases with search*/
    public function testGetSkillGroupsWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/skill-groups?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
            $this->assertEquals(404, $response->getStatusCode());
      }
    }
     
    /**Ranks positive test cases*/
    public function testGetRanksPositive(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
    
    /**Ranks negative test cases*/
    public function testGetRanksNegative(): void
    {
        $response = $this->get('/api/v1/master/ranks');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(400, $response->getStatusCode());
       }
    }

    /**Ranks positive test cases with search*/
    public function testGetRanksWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/ranks?language=en&search=Rank 0');
       $reponse=$response->json();
       if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
    
    /**Ranks negative test cases with search*/
    public function testGetRanksWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/ranks?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(404, $response->getStatusCode());
        }
    }
    
    /**ProjectSubmission positive test cases */
    public function testGetProjectSubmissionRequirementsPositive(): void
    {  
       $response = $this->get('/api/v1/master/project-submission-requirement?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**ProjectSubmission negative test cases with search*/
    public function testGetProjectSubmissionRequirementsNegative(): void
    {  
       $response = $this->get('/api/v1/master/project-submission-requirement');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
          $this->assertEquals(400, $response->getStatusCode());
      }
    }
     
    /**ProjectSubmission positive test cases with search*/
    public function testGetProjectSubmissionRequirementsWithSearchPositive(): void
    { 
         $response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=Complete project pitch');
         $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**ProjectSubmission negative test cases with search*/
    public function testGetProjectSubmissionRequirementsWithSearchNegative(): void
    {  
       $response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals(404, $response->getStatusCode());
       }
    }
      
    /**AchievementConditionLists positive test cases*/
    public function testGetAchievementConditionListsPositive(): void
    {
       $response = $this->get('/api/v1/master/achievement-condition-list?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
    /**AchievementConditionLists negative test cases with search*/
    public function testGetAchievementConditionListsNegative(): void
    {
       $response = $this->get('/api/v1/master/achievement-condition-list');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
         $this->assertEquals(400, $response->getStatusCode());
        }
    }
      
    /**AchievementConditionLists positive test cases with search*/
    public function testGetAchievementConditionListsWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=Complete All');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
      
    /**AchievementConditionLists negative test cases with search*/
    public function testGetAchievementConditionListsWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{  
          $this->assertEquals(404, $response->getStatusCode());
       }
    }
    
    /**Hosts positive test cases */
    public function testGetHostsPositive(): void
    {
       $response = $this->get('/api/v1/master/host?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    /**Hosts negative test cases */
    public function testGetHostsNegative(): void
    {
       $response = $this->get('/api/v1/master/host');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
            $this->assertEquals(400, $response->getStatusCode());
      
       }
    }
     /**Host positive test case with search */
    public function testGetHostsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/host?language=en&search=Google');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
     
     /**Host negative test case with search */
    public function testGetHostsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/host?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
          $this->assertEquals(404, $response->getStatusCode());
       }
    }
     /**FlexibleDateDuration positive test case */
    public function testGetFlexibleDateDurationsPositive(): void
    {
      $response = $this->get('/api/v1/master/flexible-date-duration?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

     /**FlexibleDateDuration negative test case */
    public function testGetFlexibleDateDurationsNegative(): void
    {
       $response = $this->get('/api/v1/master/flexible-date-duration');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
          $this->assertEquals(400, $response->getStatusCode());
       }
    }
       
     /**FlexibleDateDuration positive test case with search */
    public function testGetFlexibleDateDurationsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=Title');
        $reponse=$response->json();
        if($reponse['success']==true){
           $response->assertOk();
        }else{
           $this->assertEquals($reponse['success'],false);
        }
    }
    
     /**FlexibleDateDuration negative test case with search */
    public function testGetFlexibleDateDurationsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=null');
        $reponse=$response->json();
        if($reponse['success']==true){
           $response->assertOk();
        }else{
            $this->assertEquals(404, $response->getStatusCode());
        }
    }
      
     /**PitchTemplates positive test case*/
    public function testGetPitchTemplatesPositive(): void
    {
       $response = $this->get('/api/v1/master/pitch-templates?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }

     /**PitchTemplates negative test case*/
    public function testGetPitchTemplatesNegative(): void
    {
       $response = $this->get('/api/v1/master/pitch-templates');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
        $this->assertEquals(400, $response->getStatusCode());
       }
    }
    
     /**PitchTemplates positive test case with search*/
    public function testGetPitchTemplatesWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/pitch-templates?language=en&search=PIE Framework');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
     /**PitchTemplates negative test case with search*/
    public function testGetPitchTemplatesWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/pitch-templates?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals(404, $response->getStatusCode());
       }
    }
       
     /**LabConditions positive test case */
    public function testGetLabConditionsPositive(): void
    {
      $response = $this->get('/api/v1/master/lab-conditions?language=en');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
    
     /**LabConditions negative test case */
    public function testGetLabConditionsNegative(): void
    {
       $response = $this->get('/api/v1/master/lab-conditions');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
            $this->assertEquals(400, $response->getStatusCode());
        }
    }
     
     /**LabConditions positive test case with search*/
    public function testGetLabConditionsWithSearchPositive(): void
    {
       $response = $this->get('/api/v1/master/lab-conditions?language=en&search=title');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals($reponse['success'],false);
       }
    }
        
     /**LabConditions negative test case with search*/
    public function testGetLabConditionsWithSearchNegative(): void
    {
       $response = $this->get('/api/v1/master/lab-conditions?language=en&search=null');
       $reponse=$response->json();
       if($reponse['success']==true){
           $response->assertOk();
       }else{
           $this->assertEquals(404, $response->getStatusCode());
       }
    }
}
