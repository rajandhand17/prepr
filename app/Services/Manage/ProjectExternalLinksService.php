<?php

namespace App\Services\Manage;

use App\Models\ProjectExternalLink;
use Exception;

class ProjectExternalLinksService
{
    public function addUpdatExternalLink($request, $projectId)
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
                            }
                        }
                        if (!$projectExternalLink) {
                            if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                                $projectExternalLink = new ProjectExternalLink();
                                $projectExternalLink->project_id = $projectId;
                                $projectExternalLink->social_media_link = $request->external_links[$key];
                                $projectExternalLink->social_link_id = $value;
                                $projectExternalLink->save();
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
