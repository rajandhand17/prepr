<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Class MasterControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Master\MasterController
 */
final class MasterControllerTest extends TestCase
{
    public function test_get_categories_positive(): void
    {
        $response = $this->get('/api/v1/master/categories?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('parent_category', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Categories negative test cases */
    public function test_get_categories_negative(): void
    {
        $response = $this->get('/api/v1/master/categories');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Categories positive test cases with search*/
    public function test_get_categories_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/categories?language=en&search=Incubator');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('parent_category', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Categories negative test cases with search */
    public function test_get_categories_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/categories?language=en&search=null');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**PitchTemplates positive test case*/
    public function test_get_pitch_templates_positive(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**PitchTemplates negative test case*/
    public function test_get_pitch_templates_negative(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**PitchTemplates positive test case with search*/
    public function test_get_pitch_templates_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates?language=en&search=PIE Framework');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**PitchTemplates negative test case with search*/
    public function test_get_pitch_templates_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/pitch-templates?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**Skills positive test cases */
    public function test_get_skills_positive(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Skills negative test cases */
    public function test_get_skills_negative(): void
    {
        $response = $this->get('/api/v1/master/skills');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Skills positive test cases with Search */
    public function test_get_Skills_With_Search_Positive(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en&search=Critical thinking');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Skills negative test cases with Search */
    public function test_get_skills_without_search_negative(): void
    {
        $response = $this->get('/api/v1/master/skills?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** Tags positive test cases  */
    public function test_get_tags_positive(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/tags?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('tag_image', $data['data'][0]);
                $this->assertArrayHasKey('components', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Tags negative test cases */
    public function test_get_tags_negative(): void
    {
        $response = $this->get('/api/v1/master/tags');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Tags Positive test cases with search*/
    public function test_get_tags_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/tags?language=en&search=No Poverty');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('tag_image', $data['data'][0]);
                $this->assertArrayHasKey('components', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Tags negative test cases with search*/
    public function test_get_tags_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/tags?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectIndustries positive test cases */
    public function test_get_project_industries_positive(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectIndustries negative test cases */
    public function test_get_project_industries_negative(): void
    {
        $response = $this->get('/api/v1/master/industries');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectIndustries positive test cases with search */
    public function test_get_project_industries_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en&search=Automotive');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectIndustries negative test cases with search */
    public function test_get_project_industries_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/industries?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectTypes positive test cases */
    public function test_get_project_types_poisitive(): void
    {
        $response = $this->get('/api/v1/master/types?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectTypes negative test cases */
    public function test_get_project_types_negative(): void
    {
        $response = $this->get('/api/v1/master/types');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectTypes Positive test cases with search */
    public function test_get_project_types_with_search_poisitive(): void
    {
        $response = $this->get('/api/v1/master/types?language=en&search=Hackathon');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectTypes negative test cases with search */
    public function test_get_project_types_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/types?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectStages positive test cases */
    public function test_get_project_stages_positive(): void
    {
        $response = $this->get('/api/v1/master/stages?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectStages negative test cases */
    public function test_get_project_stages_negative(): void
    {
        /** @todo This test is complete. */
        $response = $this->get('/api/v1/master/stages');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectStages positive test cases with search */
    public function test_get_project_stages_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/stages?language=en&search=concept development');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectStages negative test cases with search */
    public function test_get_project_stages_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/stages?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectVerticals positive test cases */
    public function test_get_project_verticals_positive(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectVerticals negative test cases */
    public function test_get_project_verticals_negative(): void
    {
        $response = $this->get('/api/v1/master/verticals');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectVerticals positive test cases with search */
    public function test_get_project_verticals_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en&search=Automotive');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectVerticals negative test cases with search*/
    public function test_get_project_verticals_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectStatus positive test cases*/
    public function test_get_project_status_positive(): void
    {
        $response = $this->get('/api/v1/master/status?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectStatus negative test cases*/
    public function test_get_project_status_negative(): void
    {
        $response = $this->get('/api/v1/master/status');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectStatus positive test cases with search*/
    public function test_get_project_status_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/status?language=en&search=Active');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectStatus negative test cases with search*/
    public function test_get_project_status_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/status?language=en&search=Null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**SocialLinks positive test cases */
    public function test_get_social_links_positive(): void
    {
        $response = $this->get('/api/v1/master/links?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('icon', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SocialLinks negative test cases*/
    public function test_get_social_links_negative(): void
    {
        $response = $this->get('/api/v1/master/links');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**SocialLinks positive test cases with search*/
    public function test_get_social_links_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/links?language=en&search=facebook');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('icon', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SocialLinks negative test cases with search*/
    public function test_get_social_links_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/links?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**SkillGroups positive test cases*/
    public function test_get_skill_groups_positive(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('skill_stacks', $data['data'][0]);
                $this->assertArrayHasKey('skills', $data['data'][0]);
                $this->assertArrayHasKey('description', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SkillGroups negative test cases */
    public function test_get_skill_groups_negative(): void
    {
        $response = $this->get('/api/v1/master/skill-groups');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**SkillGroups positive test cases with search*/
    public function test_get_skill_groups_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en&search=Engineering');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SkillGroups negative test cases with search*/
    public function test_get_skill_groups_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**Ranks positive test cases*/
    public function test_get_ranks_positive(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('description', $data['data'][0]);
                $this->assertArrayHasKey('image', $data['data'][0]);
                $this->assertArrayHasKey('category', $data['data'][0]);
                $this->assertArrayHasKey('point', $data['data'][0]);
                $this->assertArrayHasKey('no_of_use', $data['data'][0]);
                $this->assertArrayHasKey('status', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Ranks negative test cases*/
    public function test_get_ranks_negative(): void
    {
        $response = $this->get('/api/v1/master/ranks');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Ranks positive test cases with search*/
    public function test_get_ranks_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en&search=Rank 0');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('description', $data['data'][0]);
                $this->assertArrayHasKey('image', $data['data'][0]);
                $this->assertArrayHasKey('category', $data['data'][0]);
                $this->assertArrayHasKey('point', $data['data'][0]);
                $this->assertArrayHasKey('no_of_use', $data['data'][0]);
                $this->assertArrayHasKey('status', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Ranks negative test cases with search*/
    public function test_get_ranks_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**ProjectSubmission positive test cases */
    public function test_get_project_submission_requirements_positive(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectSubmission negative test cases with search*/
    public function test_get_project_submission_requirements_negative(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**ProjectSubmission positive test cases with search*/
    public function test_get_project_submission_requirements_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=Complete project pitch');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**ProjectSubmission negative test cases with search*/
    public function test_get_project_submission_requirements_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**AchievementConditionLists positive test cases*/
    public function test_get_achievement_condition_lists_positive(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**AchievementConditionLists negative test cases with search*/
    public function test_get_achievement_conditionLists_negative(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**AchievementConditionLists positive test cases with search*/
    public function test_get_achievement_condition_lists_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=Complete All');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**AchievementConditionLists negative test cases with search*/
    public function test_get_achievement_condition_lists_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/achievement-condition-list?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**Hosts positive test cases */
    public function test_get_hosts_positive(): void
    {
        $response = $this->get('/api/v1/master/host?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('link', $data['data'][0]);
                $this->assertArrayHasKey('image', $data['data'][0]);
                $this->assertArrayHasKey('status', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Hosts negative test cases */
    public function test_get_hosts_negative(): void
    {
        $response = $this->get('/api/v1/master/host');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Host positive test case with search */
    public function test_get_hosts_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/host?language=en&search=Google');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
                $this->assertArrayHasKey('link', $data['data'][0]);
                $this->assertArrayHasKey('image', $data['data'][0]);
                $this->assertArrayHasKey('status', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Host negative test case with search */
    public function test_get_hosts_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/host?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**FlexibleDateDuration positive test case */
    public function test_get_flexible_date_durations_positive(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**FlexibleDateDuration negative test case */
    public function test_get_flexible_date_durations_negative(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**FlexibleDateDuration positive test case with search */
    public function test_get_flexible_date_durations_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=Title');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**FlexibleDateDuration negative test case with search */
    public function test_get_flexible_date_durations_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**LabConditions positive test case */
    public function test_get_lab_conditions_positive(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**LabConditions negative test case */
    public function test_get_lab_conditions_negative(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**LabConditions positive test case with search*/
    public function test_get_lab_conditions_with_search_positive(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en&search=title');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**LabConditions negative test case with search*/
    public function test_get_lab_conditions_with_search_negative(): void
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en&search=null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**SocialConnect positive test case */
    public function test_social_connect_positive(): void
    {
        $response = $this->get('/api/v1/master/social-connect?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SocialConnect negative test case */
    public function test_social_connect_nagtive(): void
    {
        $response = $this->get('/api/v1/master/social-connect');
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_durations_positive(): void
    {
        $response = $this->get('/api/v1/master/durations?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SocialConnect negative test case */
    public function test_durations_nagtive(): void
    {
        $response = $this->get('/api/v1/master/durations');
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_levels_positive(): void
    {
        $response = $this->get('/api/v1/master/levels?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('id', $data['data'][0]);
                $this->assertArrayHasKey('title', $data['data'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**SocialConnect negative test case */
    public function test_levels_nagtive(): void
    {
        $response = $this->get('/api/v1/master/levels');
        $this->assertEquals(400, $response->getStatusCode());
    }
}
