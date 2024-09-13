<?php

namespace Tests\Feature\Http\Controllers\Api\Profile;

use App\Models\Friend;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserTag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\BaseTestCase;

/**
 * Class ProfileControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Profile\ProfileController
 */
final class ProfileControllerTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            ...$this->parameters,
            'valid_personal_detail_data' => [
                'about'            => 'This is about.',
                'purpose'          => 12,
                'gender'           => 'decline_to_answer',
                'date_of_birth'    => '20-12-1994',
                'recent_immigrant' => 'true',
                'indigenous_group' => 'true',
                'visible_minority' => 'true',
                'disability'       => 'false',
                'address'          => 'st ds',
                'city'             => 'onertio',
                'state'            => 'state',
                'country'          => 'Canada',
                'name'             => 'John Doe Edited',
            ],
            'invalid_personal_detail_enum_data' => [
                'about'            => 'This is about.',
                'purpose'          => 12,
                'gender'           => 'invalid_gender',
                'date_of_birth'    => '20-12-1994',
                'recent_immigrant' => 'true',
                'indigenous_group' => 'invalid_data',
                'visible_minority' => 'true',
                'disability'       => 'false',
                'address'          => 'st ds',
                'city'             => 'onertio',
                'state'            => 'state',
                'country'          => 'Canada',
                'name'             => 'John Doe Edited',
            ],
            'valid_add_experience_data' => [
                'company'     => ['company 6', 'company 7'],
                'position'    => ['Developer', 'Sr. Developer'],
                'start_date'  => ['2016-12-17', '2018-03-2'],
                'end_date'    => ['2018-02-12', '2020-01-01'],
                'address'     => ['street no 5, society', 'street no 5, society'],
                'state'       => ['Punjab', 'Punjab'],
                'country'     => ['india', 'canada'],
                'description' => ['Description', 'Description'],
            ],
            'valid_add_certificate_data' => [
                'company'     => ['company 1', 'company 2'],
                'name'        => ['name 1', 'name 2'],
                'start_date'  => ['2023-12-26', '2023-12-26'],
                'end_date'    => ['2024-12-27', '2024-12-27'],
                'description' => ['This is description one', 'this is description two'],
            ],
            'valid_add_education_data' => [
                'university'          => ['Montclair', 'webster'],
                'degree'              => ['undergraduate', 'graduate'],
                'start_date'          => ['2012-01-01', '2016-01-01'],
                'end_date'            => ['2015-12-29', '2018-12-29'],
                'address'             => ['address one', 'address two'],
                'state'               => ['punjab', 'washington'],
                'country'             => ['India', 'USA'],
                'description'         => ['Adding educations', 'Adding educations'],
                'enrollment_status'   => ['no', 'no'],
                'student_number'      => ['12', '34'],
                'current_program'     => ['accounting', 'accounting'],
                'current_degree'      => ['bachelor', 'masters'],
                'current_institution' => ['stxavier', 'stxavier'],
                'institution_type'    => ['college', 'college'],
                'current_year'        => ['4', '4'],
            ],
            'valid_add_patent_data' => [
                'company'     => ['company1', 'company2'],
                'name'        => ['name1', 'name2'],
                'patent_date' => ['2022-08-01', '2022-08-03'],
                'description' => ['This is description one', 'This is description two'],
            ],
            'valid_skill_add_data' => [
                'skill_id' => $this->getAlreadyExistedSkillsId(),
                'pinned'   => [0, 1],
            ],
            'valid_tag_add_data' => [
                'tag_id' => $this->getAlreadyExistedTagsId(),
            ],
        ];
    }

    private function getAlreadyExistedSkillsId()
    {
        $skills = Skill::query()->select('id')->limit(2)->get()->toArray();

        return array_map(function ($item) {
            return $item['id'];
        }, $skills);
    }

    private function getAlreadyExistedTagsId()
    {
        $tags = Tag::query()->select('id')->limit(2)->get()->toArray();

        return array_map(function ($item) {
            return $item['id'];
        }, $tags);
    }

    private function getCreatedExperienceIds()
    {
        $validData = (array) $this->parameters['valid_add_experience_data'];
        $res = $this->post('/api/v1/profile/experience/add?language=en', $validData)->json();

        return [data_get($res, 'data.0.id'), data_get($res, 'data.1.id')];
    }

    private function getCreatedEducationIds()
    {
        $validData = (array) $this->parameters['valid_add_education_data'];
        $res = $this->post('/api/v1/profile/education/add?language=en', $validData)->json();

        return [data_get($res, 'data.0.id'), data_get($res, 'data.1.id')];
    }

    private function getCreatedCertificateIds()
    {
        $validData = (array) $this->parameters['valid_add_certificate_data'];
        $res = $this->post('/api/v1/profile/certificate/add?language=en', $validData);

        return [data_get($res, 'data.0.id'), data_get($res, 'data.1.id')];
    }

    private function getCreatedPatientIds()
    {
        $validData = (array) $this->parameters['valid_add_patent_data'];
        $res = $this->post('/api/v1/profile/patent/add?language=en', $validData)->json();

        return [data_get($res, 'data.0.id'), data_get($res, 'data.1.id')];
    }

    private function getCreatedSkillIds()
    {
        $validData = (array) $this->parameters['valid_skill_add_data'];
        $res = $this->post('/api/v1/profile/skills/add?language=en', $validData)->json();

        return [data_get($res, 'data.0.skill.id'), data_get($res, 'data.1.skill.id')];
    }

    private function getCreatedTagIds()
    {
        $validData = (array) $this->parameters['valid_tag_add_data'];
        $this->post('/api/v1/profile/tags/add?language=en', $validData)->json();
        $data = UserTag::query()->where('user_id', auth()->user()->id)->get()->toArray();

        return array_map(function ($item) {
            return $item['tag_id'];
        }, $data);
    }

    public function test_profile_show_without_language_param_negative(): void
    {
        $user = User::query()->first();
        $this->get("/api/v1/profile/$user->username")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_profile_show_with_invalid_username_negative()
    {
        $this->get('/api/v1/profile/invalid_user_name?language=en')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                ->where('message', __('responses.not_found_user_profile_detail'))
                ->where('success', false)
            );
    }

    public function test_profile_show_with_valid_username_positive()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username?language=en")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('data.id', auth()->user()->id)->etc());
    }

    public function test_add_personal_details_positive()
    {
        $validData = (array) $this->parameters['valid_personal_detail_data'];
        $this->post('/api/v1/profile/personal-detail/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('data.id', auth()->user()->id)
                    ->where('message', __('responses.add_user_personal_created'))
                    ->where('data.full_name', $validData['name'])
                    ->etc()
            );

        $this->assertDatabaseHas('users', ['id' => auth()->user()->id, 'full_name' => $validData['name']]);
    }

    public function test_add_personal_details_without_language_params_negative()
    {
        $this->post('/api/v1/profile/personal-detail/add')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_personal_details_with_invalid_enum_data_negative()
    {
        $invalidData = (array) $this->parameters['invalid_personal_detail_enum_data'];
        $this->post('/api/v1/profile/personal-detail/add?language=en', $invalidData)
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.gender', fn (AssertableJson $json) => $json->where('0', __('responses.gender_between')))
                    ->has('data.indigenous_group', fn (AssertableJson $json) => $json->where('0', __('responses.true_or_false')))
                    ->etc()
            );
    }

    public function test_add_experience_without_language_param_negative()
    {
        $this->post('/api/v1/profile/experience/add')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_experience_with_valid_data_positive()
    {
        $this->assertDatabaseCount('user_experiences', 0);
        $validData = (array) $this->parameters['valid_add_experience_data'];
        $this->post('/api/v1/profile/experience/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.add_user_experience_update'))
                    ->has(
                        'data',
                        fn (AssertableJson $json) => $json->where('0.company', $validData['company'][0])
                            ->where('1.company', $validData['company'][1])
                            ->where('0.country', $validData['country'][0])
                            ->where('1.country', $validData['country'][1])
                    )
                    ->etc()
            );
        $this->assertDatabaseCount('user_experiences', 2);
        $this->assertDatabaseHas('user_experiences', ['user_id' => auth()->user()->id, 'company' => $validData['company'][0]]);
    }

    public function test_add_experience_without_required_field_negative()
    {
        $this->post('/api/v1/profile/experience/add?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.company', fn (AssertableJson $json) => $json->where('0', __('responses.company_required')))
                    ->has('data.description', fn (AssertableJson $json) => $json->where('0', __('responses.description_required')))
                    ->etc()
            );

        $this->assertDatabaseCount('user_experiences', 0);
    }

    public function test_upload_profile_image_positive()
    {
        $this->assertDatabaseHas('users', ['id' => auth()->user()->id, 'profile_image' => null]);

        Storage::fake('profile_image');
        $file = UploadedFile::fake()->image('profile.jpg');
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/image?language=en', ['profile_image' => $file])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->etc()
            );

        $profile = DB::table('users')->where('id', auth()->user()->id)->first()->profile_image;
        $this->assertNotNull($profile);
    }

    public function test_upload_profile_without_language_param_negative()
    {
        Storage::fake('profile_image');
        $file = UploadedFile::fake()->image('profile.jpg');
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/image', ['profile_image' => $file])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_upload_profile_without_required_field()
    {
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/image?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.profile_image', fn (AssertableJson $json) => $json->where('0', __('responses.required_field')))
                    ->etc()
            );
    }

    public function test_upload_profile_with_size_more_than_allowed_negative()
    {
        Storage::fake('profile_image');
        $file = UploadedFile::fake()->image('profile.jpg')->size(5000);

        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/image?language=en', ['profile_image' => $file])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('success', false)
                    ->has('data.profile_image', fn (AssertableJson $json) => $json->where('0', __('responses.mimes_image_max')))
                    ->etc()
            );
    }

    public function test_upload_profile_with_invalid_mime_type_negative()
    {
        Storage::fake('profile_image');
        $file = UploadedFile::fake()->image('profile.jpg')->mimeType('image/apng');
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/image?language=en', ['profile_image' => $file])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('success', false)
                    ->has('data.profile_image', fn (AssertableJson $json) => $json->where('1', __('responses.mimes_image'))->etc())
                    ->etc()
            );
    }

    public function test_delete_experience_positive()
    {
        $this->assertDatabaseCount('user_experiences', 0);
        $id = $this->getCreatedExperienceIds()[0];
        $this->assertDatabaseCount('user_experiences', 2);
        $this->delete("/api/v1/profile/experience/$id/delete?language=en")->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.delete_experience'))
                    ->etc()
            );

        $this->assertSoftDeleted('user_experiences', ['id' => $id]);
    }

    public function test_delete_experience_without_language_param_negative()
    {
        $this->assertDatabaseCount('user_experiences', 0);
        $id = $this->getCreatedExperienceIds()[0];
        $this->delete("/api/v1/profile/experience/$id/delete")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );

        $this->assertNotSoftDeleted('user_experiences', ['id' => $id]);
    }

    public function test_add_certificate_with_valid_data_positive()
    {
        $validData = (array) $this->parameters['valid_add_certificate_data'];
        $this->assertDatabaseCount('user_certificates', 0);
        $this->post('/api/v1/profile/certificate/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.add_certificate_created'))
                    ->has(
                        'data',
                        fn (AssertableJson $json) => $json->where('0.company', $validData['company'][0])
                            ->where('1.company', $validData['company'][1])
                            ->where('0.name', $validData['name'][0])
                            ->where('1.name', $validData['name'][1])
                    )
                    ->etc()
            );

        $this->assertDatabaseCount('user_certificates', 2);
        $this->assertDatabaseHas('user_certificates', ['user_id' => auth()->user()->id, 'company' => $validData['company'][0]]);
    }

    public function test_add_certificate_without_language_param_negative()
    {
        $validData = (array) $this->parameters['valid_add_certificate_data'];
        $this->post('/api/v1/profile/certificate/add', $validData)
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_certificate_without_required_field_negative()
    {
        $this->post('/api/v1/profile/certificate/add?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.company', fn (AssertableJson $json) => $json->where('0', __('responses.company_required')))
                    ->has('data.start_date', fn (AssertableJson $json) => $json->where('0', __('responses.start_date_required')))
                    ->etc()
            );
        $this->assertDatabaseCount('user_certificates', 0);
    }

    public function test_add_education_with_valid_data_positive()
    {
        $this->assertDatabaseCount('user_educations', 0);
        $validData = (array) $this->parameters['valid_add_education_data'];
        $this->post('/api/v1/profile/education/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.add_user_education_created'))
                    ->has('data', fn (AssertableJson $json) => $json
                        ->where('0.university', $validData['university'][0])
                        ->where('1.university', $validData['university'][1])
                        ->where('0.address', $validData['address'][0])
                        ->where('1.address', $validData['address'][1])
                        ->etc())
            );
        $this->assertDatabaseCount('user_educations', 2);
        $this->assertDatabaseHas('user_educations', ['user_id' => auth()->user()->id, 'university' => $validData['university'][0]]);
    }

    public function test_add_education_without_language_param_negative()
    {
        $validData = (array) $this->parameters['valid_add_education_data'];
        $this->post('/api/v1/profile/education/add', $validData)
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_education_without_required_param_negative()
    {
        $this->post('/api/v1/profile/education/add?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.university', fn (AssertableJson $json) => $json->where('0', __('responses.university_required')))
                    ->has('data.degree', fn (AssertableJson $json) => $json->where('0', __('responses.university_required')))
                    ->etc()
            );
        $this->assertDatabaseCount('user_certificates', 0);
    }

    public function test_delete_education_positive()
    {
        $this->assertDatabaseCount('user_educations', 0);
        $id = $this->getCreatedEducationIds()[0];
        $this->assertDatabaseCount('user_educations', 2);
        $this->delete("/api/v1/profile/education/$id/delete?language=en")->assertOk();
        $this->assertSoftDeleted('user_educations', ['id' => $id]);
    }

    public function test_delete_education_without_language_param_negative()
    {
        $this->assertDatabaseCount('user_educations', 0);
        $id = $this->getCreatedEducationIds()[0];
        $this->assertDatabaseCount('user_educations', 2);
        $this->delete("/api/v1/profile/education/$id/delete")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
        $this->assertNotSoftDeleted('user_educations', ['id' => $id]);
    }

    public function test_file_upload_experience_positive()
    {
        $this->assertDatabaseEmpty('user_personal_files');

        Storage::fake('file');
        $file = UploadedFile::fake()->image('file.pdf');
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/file/upload?language=en', ['file' => $file])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->etc()
            );

        $this->assertDatabaseHas('user_personal_files', ['user_id' => auth()->user()->id]);
    }

    public function test_file_upload_experience_with_invalid_file_size_negative()
    {
        Storage::fake('file');
        $file = UploadedFile::fake()->image('file.pdf')->size(5000);
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/file/upload?language=en', ['file' => $file])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.file', fn (AssertableJson $json) => $json->where('0', __('responses.mimes_image_max')))
                    ->etc()
            );
    }

    public function test_file_upload_experience_without_language_param_negative()
    {
        Storage::fake('file');
        $file = UploadedFile::fake()->image('file.pdf');
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/file/upload', ['file' => $file])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_file_upload_experience_with_invalid_mime_type_negative()
    {
        Storage::fake('file');
        $file = UploadedFile::fake()->image('file.jpg');
        $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/api/v1/profile/file/upload?language=en', ['file' => $file])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                ->has('data.file', fn (AssertableJson $json) => $json->where('0', __('responses.files_mimes_image')))
                ->etc()
            );
    }

    public function test_delete_certificate_positive()
    {
        $this->assertDatabaseCount('user_certificates', 0);
        $id = $this->getCreatedCertificateIds()[0];
        $this->assertDatabaseCount('user_certificates', 2);
        $this->delete("/api/v1/profile/certificate/$id/delete?language=en")->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)->etc());
        $this->assertSoftDeleted('user_certificates', ['id' => $id]);
    }

    public function test_delete_certificate_without_language_param_negative()
    {
        $id = $this->getCreatedCertificateIds()[0];
        $this->delete("/api/v1/profile/certificate/$id/delete")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_patent_with_valid_data_positive()
    {
        $this->assertDatabaseCount('user_patents', 0);
        $validData = (array) $this->parameters['valid_add_patent_data'];
        $this->post('/api/v1/profile/patent/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.add_user_patent_created'))
                    ->etc()
            );
        $this->assertDatabaseCount('user_patents', 2);
        $this->assertDatabaseHas('user_patents', ['user_id' => auth()->user()->id, 'title' => $validData['company'][0]]);
    }

    public function test_add_patent_without_required_field_negative()
    {
        $this->post('/api/v1/profile/patent/add?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                ->has('data.company', fn (AssertableJson $json) => $json->where('0', __('responses.company_required')))
                ->has('data.description', fn (AssertableJson $json) => $json->where('0', __('responses.description_required')))
                ->etc()
            );
    }

    public function test_add_patent_without_language_param_negative()
    {
        $validData = (array) $this->parameters['valid_add_patent_data'];
        $this->post('/api/v1/profile/patent/add', $validData)
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_delete_patent_positive()
    {
        $this->assertDatabaseCount('user_patents', 0);
        $id = $this->getCreatedPatientIds()[0];
        $this->assertDatabaseCount('user_patents', 2);
        $this->delete("/api/v1/profile/patent/$id/delete?language=en")->assertOk();
        $this->assertSoftDeleted('user_patents', ['id' => $id]);
    }

    public function test_delete_patent_without_language_param_negative()
    {
        $id = $this->getCreatedPatientIds()[0];
        $this->delete("/api/v1/profile/patent/$id/delete")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_skills_to_profile_with_valid_data_positive()
    {
        $this->assertDatabaseCount('user_skills', 0);
        $validData = (array) $this->parameters['valid_skill_add_data'];
        $this->post('/api/v1/profile/skills/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.add_skills_create'))
                    ->has('data', fn (AssertableJson $json) => $json->where('0.skill.id', $validData['skill_id'][0])->etc())
                    ->has('data', fn (AssertableJson $json) => $json->where('1.skill.id', $validData['skill_id'][1])->etc())
                    ->etc()
            );
        $this->assertDatabaseCount('user_skills', 2);
        $this->assertDatabaseHas('user_skills', ['user_id' => auth()->user()->id, 'skill' => $validData['skill_id'][0]]);
    }

    public function test_add_skills_to_profile_without_language_params_negative()
    {
        $validData = (array) $this->parameters['valid_skill_add_data'];
        $this->post('/api/v1/profile/skills/add', $validData)
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_skills_to_profile_with_invalid_field_negative()
    {
        $this->post('/api/v1/profile/skills/add?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.skill_id', fn (AssertableJson $json) => $json->where('0', __('responses.skill_id_required')))
                    ->etc()
            );

        $res = $this->post('/api/v1/profile/skills/add?language=en', ['skill_id' => [99]]);
        $res->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->etc()
            );

        $this->assertTrue(data_get($res, ['data', 'skill_id.0', '0']) === __('responses.skill_id_exists'));
    }

    public function test_add_tags_to_profile_with_valid_data_positive()
    {
        $this->assertDatabaseCount('user_tags', 0);
        $validData = (array) $this->parameters['valid_tag_add_data'];
        $this->post('/api/v1/profile/tags/add?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)->etc()
            );
        $this->assertDatabaseCount('user_tags', 2);
        $this->assertDatabaseHas('user_tags', ['user_id' => auth()->user()->id, 'tag_id' => $validData['tag_id'][0]]);
    }

    public function test_add_tags_to_profile_without_language_params_negative()
    {
        $validData = (array) $this->parameters['valid_tag_add_data'];
        $this->post('/api/v1/profile/tags/add', $validData)
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_add_tags_without_required_field_negative()
    {
        $this->post('/api/v1/profile/tags/add?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.tag_id', fn (AssertableJson $json) => $json->where('0', __('responses.tag_id_required')))
                    ->etc()
            );
    }

    public function test_delete_skill_positive()
    {
        $this->assertDatabaseCount('user_skills', 0);
        $id = $this->getCreatedSkillIds()[0];
        $this->assertDatabaseCount('user_skills', 2);
        $this->delete("/api/v1/profile/skills/$id/delete?language=en")
            ->assertOk();
        $this->assertDatabaseCount('user_skills', 2);
        $this->assertSoftDeleted('user_skills', ['skill' => $id, 'user_id' => auth()->user()->id]);
    }

    public function test_delete_skill_without_language_params_negative()
    {
        $id = $this->getCreatedSkillIds()[0];
        $this->delete("/api/v1/profile/skills/$id/delete")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_delete_tag_positive()
    {
        $this->assertDatabaseCount('user_tags', 0);
        $id = $this->getCreatedTagIds()[0];
        $this->assertDatabaseCount('user_tags', 2);
        $this->delete("/api/v1/profile/tags/$id/delete?language=en")
            ->assertOk();
        $this->assertDatabaseCount('user_tags', 2);
        $this->assertSoftDeleted('user_tags', ['tag_id' => $id, 'user_id' => auth()->user()->id]);
    }

    public function test_delete_tag_without_language_param_negative()
    {
        $id = $this->getCreatedTagIds()[0];
        $this->delete("/api/v1/profile/tags/$id/delete")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_send_friend_request_with_valid_data_positive()
    {
        $this->assertDatabaseCount('friends', 0);
        $user = User::query()->where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/send?language=en', [
            'user_id' => $user->id,
        ])->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.success_send_message'))
                    ->etc()
            );
        $this->assertDatabaseCount('friends', 1);
        $this->assertDatabaseHas('friends', ['reference_id' => auth()->user()->id, 'user_id' => $user->id]);
    }

    public function test_send_friend_request_to_yourself_negative()
    {
        $this->post('/api/v1/profile/friends/request/send?language=en', [
            'user_id' => auth()->user()->id,
        ])->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->where('message', __('responses.self_request'))
                    ->etc()
            );
    }

    public function test_send_friend_request_with_invalid_data_negative()
    {
        $this->post('/api/v1/profile/friends/request/send?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.user_id', fn (AssertableJson $json) => $json->where('0', __('responses.reference_id_required')))
                    ->etc()
            );

        $this->post('/api/v1/profile/friends/request/send?language=en', ['user_id' => '99999'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.user_id', fn (AssertableJson $json) => $json->where('0', __('responses.reference_id_exists')))
                    ->etc()
            );
    }

    public function test_send_friend_request_without_language_params()
    {
        $user = User::query()->where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/send', ['user_id' => $user->id])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_send_follow_request_positive()
    {
        $user = User::query()->where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/send?language=en', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('friends', 1);
        $this->assertDatabaseHas('friends', ['reference_id' => auth()->user()->id, 'user_id' => $user->id]);
        Friend::query()->where('user_id', $user->id)->where('reference_id', auth()->user()->id)->update(['status' => '1']);
        $this->post('/api/v1/profile/friends/request/follow?language=en', ['user_id' => $user->id])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message', __('responses.success_follow_message'))
                ->where('success', true)
                ->etc()
            );
    }

    public function test_send_follow_request_without_language_param_negative()
    {
        $user = User::query()->where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/follow', ['user_id' => $user->id])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_send_follow_request_when_not_friend_positive()
    {
        $user = User::query()->where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/send?language=en', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('friends', 1);
        $this->assertDatabaseHas('friends', ['reference_id' => auth()->user()->id, 'user_id' => $user->id]);

        $this->post('/api/v1/profile/friends/request/follow?language=en', ['user_id' => $user->id])
            ->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                ->where('message', __('responses.not_friend'))
                ->etc()
            );
    }

    public function test_accept_friend_request_positive()
    {
        // prepare data friend request
        $this->assertDatabaseCount('friends', 0);
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'      => auth()->user()->id,
            'reference_id' => $user->id,
        ]);
        $this->assertDatabaseCount('friends', 1);

        $this->post('/api/v1/profile/friends/request/accept?language=en', [
            'user_id' => $user->id,
        ])->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.accept_friend_request'))
                    ->etc()
            );
    }

    public function test_accept_friend_request_without_valid_data_negative()
    {
        $this->post('/api/v1/profile/friends/request/accept?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('success', false)
                    ->has('data.user_id', fn (AssertableJson $json) => $json->where('0', __('responses.reference_id_required')))
                    ->etc()
            );
    }

    public function test_accept_friend_request_without_language_param_negative()
    {
        $user = User::where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/accept', [
            'user_id' => $user->id,
        ])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_reject_friend_request_positive()
    {
        // prepare data friend request
        $this->assertDatabaseCount('friends', 0);
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'      => auth()->user()->id,
            'reference_id' => $user->id,
        ]);
        $this->assertDatabaseCount('friends', 1);

        $this->post('/api/v1/profile/friends/request/reject?language=en', [
            'user_id' => $user->id,
        ])->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.reject_friend_request'))
                    ->etc()
            );
    }

    public function test_reject_friend_request_without_valid_data_negative()
    {
        $this->post('/api/v1/profile/friends/request/reject?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('success', false)
                    ->has('data.user_id', fn (AssertableJson $json) => $json->where('0', __('responses.reference_id_required')))
                    ->etc()
            );
    }

    public function test_reject_friend_request_without_language_param_negative()
    {
        $user = User::where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/reject', [
            'user_id' => $user->id,
        ])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_friend_listing_postive()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends?language=en")->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.friends_listing'))
                    ->etc()
            );
    }

    public function test_friend_listing_without_language_param()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_followers_listing_postive()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/followers?language=en")->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.friends_listing'))
                    ->etc()
            );
    }

    public function test_followers_listing_without_language_param()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/followers")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_pending_responses_with_no_pending_request_positive()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/pending?language=en")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)->etc());
    }

    public function test_pending_responses_positive()
    {
        // create pending friend request
        $this->assertDatabaseCount('friends', 0);
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'      => auth()->user()->id,
            'reference_id' => $user->id,
        ]);
        $this->assertDatabaseCount('friends', 1);

        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/pending?language=en")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.friends_listing'))
                    ->has('data', fn (AssertableJson $json) => $json->where('0.id', $user->id)->where('0.first_name', $user->first_name)->etc())
                    ->etc()
            );
    }

    public function test_pending_responses_without_language_param_negative()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/pending")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_follow_list_positive()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/follow?language=en")->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.friends_listing'))
                    ->etc()
            );
    }

    public function test_follow_list_without_language_param()
    {
        $username = auth()->user()->username;
        $this->get("/api/v1/profile/$username/friends/follow")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_unfollow_positive()
    {
        // prepare data in which both user follow each others.
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'          => $user->id,
            'reference_id'     => auth()->user()->id,
            'status'           => '1',
            'user_follow'      => '2',
            'reference_follow' => '2',
        ]);

        $this->post('/api/v1/profile/friends/request/un-follow?language=en', ['user_id' => $user->id])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.unfollow_friend_successfully'))
                    ->etc()
            );
    }

    public function test_unfollow_without_following_other_user_negative()
    {
        // prepare data in which both user follow each others.
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'          => $user->id,
            'reference_id'     => auth()->user()->id,
            'status'           => '1',
            'user_follow'      => '1',
            'reference_follow' => '1',
        ]);
        $this->post('/api/v1/profile/friends/request/un-follow?language=en', ['user_id' => $user->id])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                ->where('message', __('responses.not_follow_status'))
                ->etc()
            );
    }

    public function test_unfollow_without_language_param()
    {
        $user = User::where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/un-follow', ['user_id' => $user->id])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_unfriend_positive()
    {
        // prepare data in which both user follow each others.
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'          => $user->id,
            'reference_id'     => auth()->user()->id,
            'status'           => '1',
            'user_follow'      => '0',
            'reference_follow' => '0',
        ]);

        $this->post('/api/v1/profile/friends/request/un-friend?language=en', ['user_id' => $user->id])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.remove_friend_successfully'))
                    ->etc()
            );
    }

    public function test_unfriend_without_request_accepted_by_other_user_negative()
    {
        // prepare data in which both user follow each others.
        $user = User::where('id', '!=', auth()->user()->id)->first();
        Friend::query()->create([
            'user_id'          => $user->id,
            'reference_id'     => auth()->user()->id,
            'status'           => '0',
            'user_follow'      => '0',
            'reference_follow' => '0',
        ]);
        $this->post('/api/v1/profile/friends/request/un-friend?language=en', ['user_id' => $user->id])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                ->where('message', __('responses.not_friend_status'))
                ->etc()
            );
    }

    public function test_unfriend_without_language_param()
    {
        $user = User::where('id', '!=', auth()->user()->id)->first();
        $this->post('/api/v1/profile/friends/request/un-follow', ['user_id' => $user->id])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }
}
