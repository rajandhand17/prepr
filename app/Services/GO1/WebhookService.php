<?php

namespace App\Services\GO1;

use App\Models\Go1WebhookMetadata;
use App\Models\ResourceModule;
use App\Models\User;
use App\Models\UserResourceProgressTracking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService extends BaseService
{
    public function webhook($payload)
    {
        try {
            $go1UserId = $payload['data']['user_id'];
            $go1ResourceId = $payload['data']['lo_id'];
            $user = User::where('go1_id', $go1UserId)->first();
            $resource = ResourceModule::where('go1_course_id', $go1ResourceId)->first();
            if (!$user) {
                throw new Exception('User does not exists');
            }

            if (!$resource) {
                throw new Exception('Resources does not exists');
            }

            $type = $payload['type'];

            if (!($type === 'enrolment.create' || $type === 'enrolment.update')) {
                return ['message' => 'unwanted event'];
            }

            $data = UserResourceProgressTracking::query()->updateOrCreate([
                'resource_module_id' => $resource->id,
                'user_id'            => $user->id,
            ], [
                'completion_status' => data_get($payload, 'data.status'),
                'lesson_status'     => data_get($payload, 'data.pass') === 1 ? 'pass' : 'fail',
                'score_raw'         => data_get($payload, 'data.result'),
                'session_time'      => data_get($payload, 'data.completed_time'),
            ]);

            $parentData = $data->first();

            if (!$parentData) {
                throw new Exception('No parent data');
            }

            Go1WebhookMetadata::create([
                'type'                               => $type,
                'fired_at'                           => Carbon::parse($payload['fired_at'] ?? Carbon::now()),
                'metadata'                           => $payload,
                'user_resource_progress_tracking_id' => $parentData['id'],
            ]);

            return true;
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    private function isValidUrl($url): bool
    {
        return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
    }

    private function removeLastSlash($url)
    {
        if (substr($url, -1) === '/') {
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    private function getDefaultUrl()
    {
        $appUrl = $this->removeLastSlash(config('app.url'));

        return $appUrl.'/api/v1/go1/webhook';
    }

    public function registerWebhookToGO1($url = '')
    {
        try {
            if (empty($url)) {
                $url = $this->getDefaultUrl();
            }

            if (!$this->isValidUrl($url)) {
                throw new Exception('Not a valid webhook url');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
                'Accept'        => 'application/json',
            ])->post("$this->endPointBaseUrl/webhooks", [
                'enrollment_create'    => true,
                'enrollment_delete'    => true,
                'enrollment_update'    => true,
                'lo_create'            => false,
                'lo_delete'            => false,
                'lo_update'            => false,
                'enabled'              => true,
                'url'                  => $url,
                'user_create'          => false,
                'user_delete'          => false,
                'user_update'          => false,
                'content_update'       => false,
                'content_decommission' => false,
            ]);

            if ($response->status() >= 400) {
                throw new Exception("status: {$response->status()}---{$response->body()}");
            }

            return $response->json();
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }
}
