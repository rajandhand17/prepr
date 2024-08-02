<?php

namespace App\Http\Resources\Public\ResourceModule;

use App\Http\Resources\Public\Scorm\ScormResource;
use App\Services\Public\ResourceModuleDetailService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $skills = null;
        $skill_groups = null;
        $skill_stacks = null;
        $links = null;
        $files = null;
        $documents = null;
        $videos = null;
        $audios = null;
        $privacy = null;
        $status = null;
        $is_global = null;
        $embedded_media = null;
        $module_progress = null;

        if ($this->urls) {
            $links = $this->urls->map(function ($index) {
                $checkResourceAssetCompletedOrNot = 'no';
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    $assetId = $index->id;
                    $checkResourceAssetCompletedOrNot = ResourceModuleDetailService::checkResourceAssetCompletedOrNot($userId, $assetId);
                }

                return [
                    'id'              => $index->id,
                    'title'           => $index->title,
                    'path'            => $index->getRawOriginal('path'),
                    'social_link_id'  => $index->social_link_id,
                    'completed'       => $checkResourceAssetCompletedOrNot,
                ];
            })->all();
        }
        if ($this->images) {
            $files = $this->images->map(function ($file) {
                $checkResourceAssetCompletedOrNot = 'no';
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    $assetId = $file->id;
                    $checkResourceAssetCompletedOrNot = ResourceModuleDetailService::checkResourceAssetCompletedOrNot($userId, $assetId);
                }

                return [
                    'id'              => $file->id,
                    'title'           => $file->title,
                    'path'            => $file->path,
                    'completed'       => $checkResourceAssetCompletedOrNot,
                ];
            });
        }
        if ($this->documents) {
            $documents = $this->documents->map(function ($document) {
                $checkResourceAssetCompletedOrNot = 'no';
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    $assetId = $document->id;
                    $checkResourceAssetCompletedOrNot = ResourceModuleDetailService::checkResourceAssetCompletedOrNot($userId, $assetId);
                }

                return [
                    'id'              => $document->id,
                    'title'           => $document->title,
                    'path'            => $document->path,
                    'completed'       => $checkResourceAssetCompletedOrNot,
                ];
            });
        }
        if ($this->videos) {
            $videos = $this->videos->map(function ($video) {
                $checkResourceAssetCompletedOrNot = 'no';
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    $assetId = $video->id;
                    $checkResourceAssetCompletedOrNot = ResourceModuleDetailService::checkResourceAssetCompletedOrNot($userId, $assetId);
                }

                return [
                    'id'              => $video->id,
                    'title'           => $video->title,
                    'path'            => $video->path,
                    'completed'       => $checkResourceAssetCompletedOrNot,
                ];
            });
        }
        if ($this->audios) {
            $audios = $this->audios->map(function ($audio) {
                $checkResourceAssetCompletedOrNot = 'no';
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    $assetId = $audio->id;
                    $checkResourceAssetCompletedOrNot = ResourceModuleDetailService::checkResourceAssetCompletedOrNot($userId, $assetId);
                }

                return [
                    'id'              => $audio->id,
                    'title'           => $audio->title,
                    'path'            => $audio->path,
                    'completed'       => $checkResourceAssetCompletedOrNot,
                ];
            });
        }

        if ($this->embedded_medias) {
            $embedded_media = $this->embedded_medias->map(function ($index) {
                $checkResourceAssetCompletedOrNot = 'no';
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    $assetId = $index->id;
                    $checkResourceAssetCompletedOrNot = ResourceModuleDetailService::checkResourceAssetCompletedOrNot($userId, $assetId);
                }
                $media_type = null;
                switch ($index->type) {
                    case '3':
                        $media_type = 'embedded_video';
                        break;
                    case '4':
                        $media_type = 'embedded_audio';
                        break;
                }

                return [
                    'id'                => $index->id,
                    'type'              => $media_type,
                    'path'              => $index->getRawOriginal('path'),
                    'completed'         => $checkResourceAssetCompletedOrNot,
                ];
            })->all();
        }

        switch($this->privacy) {
            case '0':
                $privacy = 'no';
                break;
            case '1':
                $privacy = 'yes';
                break;
            default:
                $privacy = 'no';
                break;
        }

        switch($this->status) {
            case '0':
                $status = 'draft';
                break;
            case '1':
                $status = 'published';
                break;
            case '2':
                $status = 'archive';
                break;
            default:
                $status = 'draft';
                break;
        }
        switch($this->is_global) {
            case '0':
                $is_global = 'no';
                break;
            case '1':
                $is_global = 'yes';
                break;
            default:
                $is_global = 'no';
                break;
        }

        if ($this->durations) {
            $duration = $this->durations->title;
            $duration_id = $this->durations->id;
        }

        if ($this->levels) {
            $level = $this->levels->title;
            $level_id = $this->levels->id;
        }

        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('foreign_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        if ($this->skill_groups) {
            $associatedSkillGroups = $this->skill_groups->pluck('foreign_id');
            $skill_groups = SkillGroupService::getSkillGroupsBasedOnIds($associatedSkillGroups)->pluck('title', 'id');

            if ($skill_groups->isEmpty()) {
                $skill_groups = $this->skill_groups->pluck('foreign_id');
            }
        }

        if ($this->skill_stacks) {
            $associatedSkillStacks = $this->skill_stacks->pluck('foreign_id');
            $skill_stacks = SkillStackService::getSkillStacksBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        $rating = intval('0');
        if (auth('api')->check()) {
            if ($this->resource_rating) {
                $rating = intval($this->resource_rating->rating);
            }

            $module_status = 'not_started';
            $module_progress = [
                'status'        => $module_status,
                'percentage'    => '0',
            ];
            if ($this->resource_module_completion_status) {
                switch ($this->resource_module_completion_status->status) {
                    case '0':
                        $module_status = 'not_started';
                        break;
                    case '1':
                        $module_status = 'in_progress';
                        break;
                    case '2':
                        $module_status = 'completed';
                        break;
                }

                $module_progress = [
                    'status'        => $module_status,
                    'percentage'    => $this->resource_module_completion_status->percentage,
                ];
            }
        }

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'title'                 => $this->title,
            'user'                  => $this->users != null ? $this->users->first_name.' '.$this->users->last_name : null,
            'organization_id'       => $this->organization != null ? $this->organization->uuid : null,
            'organization'          => $this->organization != null ? $this->organization->title : null,
            'duration'              => $duration,
            'duration_id'           => $duration_id,
            'level'                 => $level,
            'level_id'              => $level_id,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'media_type'            => $this->media_type,
            'cover_image'           => $this->media,
            'privacy'               => $privacy,
            'status'                => $status,
            'is_global'             => $is_global,
            'scorm'                 => new ScormResource($this->scorm?->select(['uuid', 'title', 'version'])->first()),
            'skills'                => $skills,
            'skill_groups'          => $skill_groups,
            'skill_stacks'          => $skill_stacks,
            'links'                 => $links,
            'files'                 => $files,
            'documents'             => $documents,
            'videos'                => $videos,
            'audios'                => $audios,
            'embedded_media'        => $embedded_media,
            'rating'                => $rating,
            'likes'                 => $this->likes()->count(),
            'shares'                => $this->shares()->count(),
            'liked'                 => $this->liked(),
            'favourite'             => $this->favorites(),
            'module_progress'       => $module_progress,
            'is_accessible'         => ($this->is_accessible == '1') ? 'yes' : 'no',
        ];
    }
}
