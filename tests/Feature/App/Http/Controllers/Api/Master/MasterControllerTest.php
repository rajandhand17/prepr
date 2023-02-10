<?php

namespace Tests\Feature\Http\Controllers\Api\Master;

use Tests\TestCase;

/**
 * Class MasterControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Master\MasterController
 */
final class MasterControllerTest extends TestCase
{
    /**Categories positive test */
    public function testGetCategoriesPositive(): void
    {
        $response = $this->get('/api/v1/master/categories?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('parent_category', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Categories negative test cases */
    public function testGetCategoriesNegative(): void
    {
        $response = $this->get('/api/v1/master/categories');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Categories positive test cases with search*/
    public function testGetCategoriesWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/categories?language=en&search=Co-working Space');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('parent_category', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Categories negative test cases with search */
    public function testGetCategoriesWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/categories?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**Skills positive test cases */
    public function testGetSkillsPositive(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Skills negative test cases */
    public function testGetSkillsNegative(): void
    {
        $response = $this->get('/api/v1/master/skills');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Skills positive test cases with Search */
    public function testGetSkillsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en&search=Critical thinking');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Skills negative test cases with Search */
    public function testGetSkillsWithoutSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** Tags positive test cases  */
    public function testGetTagsPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/tags?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('tag_image', $data['data'][0]);
            $this->assertArrayHasKey('components', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Tags negative test cases */
    public function testGetTagsNegative(): void
    {
        $response = $this->get('/api/v1/master/tags');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Tags Positive test cases with search*/
    public function testGetTagsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/tags?language=en&search=No Poverty');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('tag_image', $data['data'][0]);
            $this->assertArrayHasKey('components', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Tags negative test cases with search*/
    public function testGetTagsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/tags?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**ProjectIndustries positive test cases */
    public function testGetProjectIndustriesPositive(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectIndustries negative test cases */
    public function testGetProjectIndustriesNegative(): void
    {
        $response = $this->get('/api/v1/master/industries');
        $this->assertEquals(400, $response->getStatusCode());
    }
    /**ProjectIndustries positive test cases with search */
    public function testGetProjectIndustriesWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en&search=Automotive');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**ProjectIndustries negative test cases with search */
    public function testGetProjectIndustriesWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }
    /**ProjectTypes positive test cases */
    public function testGetProjectTypesPoisitive(): void
    {
        $response = $this->get('/api/v1/master/types?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }
    /**ProjectTypes negative test cases */
    public function testGetProjectTypesNegative(): void
    {
        $response = $this->get('/api/v1/master/types');
        $this->assertEquals(400, $response->getStatusCode());
    }
    /**ProjectTypes Positive test cases with search */
    public function testGetProjectTypesWithSearchPoisitive(): void
    {
        $response = $this->get('/api/v1/master/types?language=en&search=Hackathon');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }
    /**ProjectTypes negative test cases with search */
    public function testGetProjectTypesWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/types?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**ProjectStages positive test cases */
    public function testGetProjectStagesPositive(): void
    {
        $response = $this->get('/api/v1/master/stages?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**ProjectStages negative test cases */
    public function testGetProjectStagesNegative(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/stages');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectStages positive test cases with search */
    public function testGetProjectStagesWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/stages?language=en&search=concept development');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectStages negative test cases with search */
    public function testGetProjectStagesWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/stages?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**ProjectVerticals positive test cases */
    public function testGetProjectVerticalsPositive(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**ProjectVerticals negative test cases */
    public function testGetProjectVerticalsNegative(): void
    {
        $response = $this->get('/api/v1/master/verticals');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectVerticals positive test cases with search */
    public function testGetProjectVerticalsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en&search=Automotive');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**ProjectVerticals negative test cases with search*/
    public function testGetProjectVerticalsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**ProjectStatus positive test cases*/
    public function testGetProjectStatusPositive(): void
    {
        $response = $this->get('/api/v1/master/status?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }


    /**ProjectStatus negative test cases*/
    public function testGetProjectStatusNegative(): void
    {
        $response = $this->get('/api/v1/master/status');
        $this->assertEquals(400, $response->getStatusCode());
    }


    /**ProjectStatus positive test cases with search*/
    public function testGetProjectStatusWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/status?language=en&search=Active');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }


    /**ProjectStatus negative test cases with search*/
    public function testGetProjectStatusWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/status?language=en&search=Null');
        $this->assertEquals(404, $response->getStatusCode());
    }


    /**SocialLinks positive test cases */
    public function testGetSocialLinksPositive(): void
    {
        $response = $this->get('/api/v1/master/links?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('icon', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**SocialLinks negative test cases*/
    public function testGetSocialLinksNegative(): void
    {
        $response = $this->get('/api/v1/master/links');
        $this->assertEquals(400, $response->getStatusCode());
    }


    /**SocialLinks positive test cases with search*/
    public function testGetSocialLinksWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/links?language=en&search=facebook');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('icon', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**SocialLinks negative test cases with search*/
    public function testGetSocialLinksWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/links?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**SkillGroups positive test cases*/
    public function testGetSkillGroupsPositive(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
            $this->assertArrayHasKey('skill_stacks', $data['data'][0]);
            $this->assertArrayHasKey('skills', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**SkillGroups negative test cases */
    public function testGetSkillGroupsNegative(): void
    {
        $response = $this->get('/api/v1/master/skill-groups');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**SkillGroups positive test cases with search*/
    public function testGetSkillGroupsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en&search=Title');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
            $this->assertArrayHasKey('skill_stacks', $data['data'][0]);
            $this->assertArrayHasKey('skills', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**SkillGroups negative test cases with search*/
    public function testGetSkillGroupsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**Ranks positive test cases*/
    public function testGetRanksPositive(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
            $this->assertArrayHasKey('image', $data['data'][0]);
            $this->assertArrayHasKey('category', $data['data'][0]);
            $this->assertArrayHasKey('point', $data['data'][0]);
            $this->assertArrayHasKey('no_of_use', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**Ranks negative test cases*/
    public function testGetRanksNegative(): void
    {
        $response = $this->get('/api/v1/master/ranks');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Ranks positive test cases with search*/
    public function testGetRanksWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en&search=Rank 0');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
            $this->assertArrayHasKey('image', $data['data'][0]);
            $this->assertArrayHasKey('category', $data['data'][0]);
            $this->assertArrayHasKey('point', $data['data'][0]);
            $this->assertArrayHasKey('no_of_use', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**Ranks negative test cases with search*/
    public function testGetRanksWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**ProjectSubmission positive test cases */
    public function testGetProjectSubmissionRequirementsPositive(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**ProjectSubmission negative test cases with search*/
    public function testGetProjectSubmissionRequirementsNegative(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectSubmission positive test cases with search*/
    public function testGetProjectSubmissionRequirementsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=Complete project pitch');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**ProjectSubmission negative test cases with search*/
    public function testGetProjectSubmissionRequirementsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**AchievementConditionLists positive test cases*/
    public function testGetAchievementConditionListsPositive(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**AchievementConditionLists negative test cases with search*/
    public function testGetAchievementConditionListsNegative(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**AchievementConditionLists positive test cases with search*/
    public function testGetAchievementConditionListsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=Complete All');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**AchievementConditionLists negative test cases with search*/
    public function testGetAchievementConditionListsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**Hosts positive test cases */
    public function testGetHostsPositive(): void
    {
        $response = $this->get('/api/v1/master/host?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('link', $data['data'][0]);
            $this->assertArrayHasKey('image', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
        } else {
            $this->fail();
        }
    }
    /**Hosts negative test cases */
    public function testGetHostsNegative(): void
    {
        $response = $this->get('/api/v1/master/host');
        $this->assertEquals(400, $response->getStatusCode());
    }
    /**Host positive test case with search */
    public function testGetHostsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/host?language=en&search=Google');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('link', $data['data'][0]);
            $this->assertArrayHasKey('image', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**Host negative test case with search */
    public function testGetHostsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/host?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }
    /**FlexibleDateDuration positive test case */
    public function testGetFlexibleDateDurationsPositive(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**FlexibleDateDuration negative test case */
    public function testGetFlexibleDateDurationsNegative(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**FlexibleDateDuration positive test case with search */
    public function testGetFlexibleDateDurationsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=Title');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**FlexibleDateDuration negative test case with search */
    public function testGetFlexibleDateDurationsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**PitchTemplates positive test case*/
    public function testGetPitchTemplatesPositive(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**PitchTemplates negative test case*/
    public function testGetPitchTemplatesNegative(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**PitchTemplates positive test case with search*/
    public function testGetPitchTemplatesWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates?language=en&search=PIE Framework');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }
    /**PitchTemplates negative test case with search*/
    public function testGetPitchTemplatesWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**LabConditions positive test case */
    public function testGetLabConditionsPositive(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**LabConditions negative test case */
    public function testGetLabConditionsNegative(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**LabConditions positive test case with search*/
    public function testGetLabConditionsWithSearchPositive(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en&search=title');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    /**LabConditions negative test case with search*/
    public function testGetLabConditionsWithSearchNegative(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**SocialConnect positive test case */
    public function testSocialConnectPositive(): void
    {
        $response = $this->get('/api/v1/master/social-connect?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
        } else {
            $this->fail();
        }
    }

    
    /**SocialConnect negative test case */
    public function testSocialConnectNagtive(): void
    {
        $response = $this->get('/api/v1/master/social-connect');
        $this->assertEquals(400, $response->getStatusCode());
    }
}
