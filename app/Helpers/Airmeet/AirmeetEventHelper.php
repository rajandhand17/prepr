<?php

namespace App\Helpers\Airmeet;

use App\Helpers\UtilityHelper;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

class AirmeetEventHelper extends AirmeetBaseHelper
{
    /**
     * @param string $id
     *
     * @return false|PromiseInterface|Response
     */
    public static function getAirmeetEventInfo(string $id): false|PromiseInterface|Response
    {
        try {
            $url = sprintf(config('airmeet.airmeet_event_info_url'), $id);

            return self::get($url);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    /**
     * @param string $airmeetEventId
     * @param array  $data
     *
     * @return array|mixed
     */
    public static function addAttendeeToEvent(string $airmeetEventId, array $data): mixed
    {
        try {
            $url = sprintf(config('airmeet.airmeet_add_event_attendee_url'), $airmeetEventId);
            $requestData = [
                'email'            => data_get($data, 'email'),
                'firstName'        => data_get($data, 'first_name'),
                'lastName'         => data_get($data, 'last_name'),
                'attendance_type'  => 'VIRTUAL',
                'city'             => data_get($data, 'city', '-'),
                'country'          => data_get($data, 'country', '-'),
                'designation'      => data_get($data, 'designation'),
                'organisation'     => data_get($data, 'organisation'),
                'registerAttendee' => false,
                'sendEmailInvite'  => true,
            ];
            $eventJoinDetails = self::post($url, $requestData);
            if ($eventJoinDetails === false) {
                return false;
            }

            return data_get($eventJoinDetails, 'entryLink');
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
