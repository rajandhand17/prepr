<?php

namespace App\Services\Manage;

use App\Helpers\Airmeet\AirmeetEventHelper;
use App\Helpers\UtilityHelper;
use App\Models\AirmeetEvent;
use App\Models\AirmeetEventAttendee;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;

class AirmeetEventService
{
    /**
     * @param string $eventId
     *
     * @return false|PromiseInterface|Response
     */
    public function getVerifiedEventDetails(string $eventId): false|PromiseInterface|Response
    {
        try {
            return AirmeetEventHelper::getAirmeetEventInfo($eventId);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param $eventUrl
     *
     * @return string|null
     */
    private function extractEventIdFromUrl($eventUrl): ?string
    {
        $pattern = '/\/(?:event|e)\/([a-f0-9-]+)/';
        if ($eventUrl) {
            preg_match($pattern, $eventUrl, $matches);
            if ($matches && isset($matches[1])) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * @param string $model
     * @param int    $model_id
     * @param array  $data
     *
     * @return false|Builder|Model
     */
    public function createUpdateEvent(string $model, int $model_id, array $data): Model|Builder|false
    {
        try {
            $eventUrl = data_get($data, 'live_event_url');
            if ($eventUrl) {
                $airmeetId = $this->extractEventIdFromUrl($eventUrl);

                /**
                 * CHECK EXISTING AIRMEET EXIST.
                 */
                $existing = AirmeetEvent::query()->where(
                    [
                        'model_type'       => $model,
                        'model_id'         => $model_id,
                        'airmeet_event_id' => $airmeetId,
                    ]
                )->first();

                /**
                 * IF NO CHANGES HAS BEEN MADE.
                 */
                if ($existing) {
                    return $existing;
                }

                /**
                 * CREATES OR UPDATES (WHEN EVENT URL IS CHANGED) AIRMEET EVENT.
                 */
                $airmeet = AirmeetEvent::query()->updateOrCreate([
                    'model_type' => $model,
                    'model_id'   => $model_id,
                ], [
                    'airmeet_event_id'  => $airmeetId,
                    'airmeet_event_url' => $eventUrl,
                ]);

                /**
                 * IF THE EVENT URL IS UPDATED DELETE ALL THE OLD INVITES.
                 */
                AirmeetEventAttendee::query()->where('airmeet_event_id', '=', data_get($airmeet, 'id'))->delete();

                return $airmeet;
            }

            return false;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
