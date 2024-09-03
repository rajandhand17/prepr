<?php

namespace App\Services\Public;

use App\Helpers\Airmeet\AirmeetEventHelper;
use App\Helpers\UtilityHelper;
use App\Models\AirmeetEvent;
use App\Models\AirmeetEventAttendee;
use Illuminate\Support\Facades\DB;

class AirmeetEventService
{
    /**
     * @param AirmeetEvent $event
     * @param array        $data
     *
     * @return AirmeetEventAttendee|false
     */
    public function store(AirmeetEvent $event, array $data): AirmeetEventAttendee|false
    {
        DB::beginTransaction();

        try {
            $airmeetEventId = data_get($event, 'airmeet_event_id');
            $eventUrl = AirmeetEventHelper::addAttendeeToEvent($airmeetEventId, [
                'email'        => data_get($data, 'email', '-'),
                'first_name'   => data_get($data, 'first_name', '-'),
                'last_name'    => data_get($data, 'last_name', '-'),
                'designation'  => 'Member',
                'organisation' => data_get($data, 'organisation', 'Prepr'),
            ]);

            if ($eventUrl === false) {
                DB::rollBack();

                return false;
            }
            /** @var AirmeetEventAttendee $attendee */
            $attendee = AirmeetEventAttendee::query()->create([
                'attendee_id'        => data_get($data, 'user_id'),
                'airmeet_event_id'   => data_get($event, 'id'),
                'airmeet_event_uuid' => data_get($event, 'airmeet_event_id'),
                'event_url'          => $eventUrl,
            ]);
            DB::commit();

            return $attendee;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            DB::rollBack();

            return false;
        }
    }

    /**
     * @param AirmeetEvent $event
     * @param array        $data
     *
     * @return string
     */
    public function getMeetUrl(AirmeetEvent $event, array $data): string
    {
        try {
            $existingAttendeeDetail = AirmeetEventAttendee::query()->where([
                'airmeet_event_id' => data_get($event, 'id'),
                'attendee_id'      => data_get($data, 'user_id'),
            ])->first();

            if ($existingAttendeeDetail) {
                return data_get($existingAttendeeDetail, 'event_url');
            }

            $newAssignment = $this->store($event, $data);

            if ($newAssignment === false) {
                return false;
            }

            return data_get($newAssignment, 'event_url');
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
