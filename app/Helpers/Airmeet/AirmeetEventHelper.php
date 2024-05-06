<?php

namespace App\Helpers\Airmeet;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

class AirmeetEventHelper extends AirmeetBaseHelper
{


    /**
     * @param string $id
     * @return false|PromiseInterface|Response
     */
    static public function getAirmeetEventInfo(string $id): false|PromiseInterface|Response
    {
        try {
            $url = sprintf('prod/airmeet/%s/info', $id);
            return self::get($url);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $airmeetEventId
     * @param array $data
     * @return array|mixed
     */
    static public function addAttendeeToEvent(string $airmeetEventId, array $data): mixed
    {
        try {
            $url = sprintf('prod/airmeet/%s/attendee', $airmeetEventId);
            $requestData = [
                'email' => data_get($data, 'email'),
                'firstName' => data_get($data, 'first_name'),
                'lastName' => data_get($data, 'last_name'),
                'attendance_type' => 'VIRTUAL',
                'city' => data_get($data, 'city', '-'),
                'country' => data_get($data, 'country', '-'),
                'designation' => data_get($data, 'designation'),
                'organisation' => data_get($data, 'organisation'),
                'registerAttendee' => false,
                'sendEmailInvite' => true,
            ];
            $eventJoinDetails = self::post($url, $requestData);
            if ($eventJoinDetails === false) {
                return false;
            }
            return data_get($eventJoinDetails, 'entryLink');
        } catch (\Exception $exception) {
            return false;
        }
    }
}
