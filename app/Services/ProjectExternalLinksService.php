<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ProjectExternalLink;
use Exception;

class ProjectExternalLinksService
{
    public function addUpdateExternalLink($request, $projectId)
    {
        try {
            if ($request->has('external_links') && $request->get('external_link_ids')) {
                if (count($request->external_link_ids) > 0) {
                    $existExternalLinks = ProjectExternalLink::where('project_id', $projectId)->pluck('social_link_id')->toArray();
                    $nonExistingIds = array_diff($existExternalLinks, $request->external_link_ids);
                    $deleteNonExistingExternalLinks = ProjectExternalLink::where('project_id', $projectId)->whereIn('social_link_id', $nonExistingIds)->delete();
                    foreach ($request->external_link_ids as $key => $value) {
                        $projectExternalLink = ProjectExternalLink::select('id', 'social_media_link')->where([
                            ['project_id', '=', $projectId],
                            ['social_link_id', '=', $value],
                        ])->first();
                        if ($projectExternalLink) {
                            if ($projectExternalLink['social_media_link'] !== $request->external_links[$key]) {
                                $projectExternalLink->social_media_link = $request->external_links[$key];
                                $projectExternalLink->save();
                                if ($projectExternalLink) {
                                    $activity = auth()->user()->full_name.' '.__('responses.project_updated_social_activty').' '.$request->external_links[$key];
                                    ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);
                                }
                            }
                        }
                        if (!$projectExternalLink) {
                            if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                                $projectExternalLink = new ProjectExternalLink();
                                $projectExternalLink->project_id = $projectId;
                                $projectExternalLink->social_media_link = $request->external_links[$key];
                                $projectExternalLink->social_link_id = $value;
                                $projectExternalLink->save();
                                if ($projectExternalLink) {
                                    $activity = auth()->user()->full_name.' '.__('responses.project_added_new_social_activty').' '.$request->external_links[$key];
                                    ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);
                                }
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function checkProjectExternalLink($projectId)
    {
        try {
            $checkProjectExternalLinks = ProjectExternalLink::where('project_id', $projectId)->count();
            $projectExternalLinks = false;
            if ($checkProjectExternalLinks > 0) {
                $projectExternalLinks = true;
            }

            return $projectExternalLinks;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deleteProjectMediaLink($request, $projectId)
    {
        try {
            $getProjectExternalLink = ProjectExternalLink::where(['id' => $request->media_id, 'project_id' => $projectId]);
            $linkName = $getProjectExternalLink->first()->social_media_link;
            if ($getProjectExternalLink->delete()) {
                $activity = auth()->user()->full_name.' '.__('responses.project_deleted_social_activty').' '.$linkName;
                ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
