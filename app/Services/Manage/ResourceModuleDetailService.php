<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ResourceModuleDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceModuleDetailService
{
    public function insertRecords($resource_module_id, $title, $type, $path, $social_link_id)
    {
        try {
            $resourceModuleDetailed = new ResourceModuleDetail();
            $resourceModuleDetailed->resource_module_id = $resource_module_id;
            $resourceModuleDetailed->title = $title;
            $resourceModuleDetailed->type = $type;
            $resourceModuleDetailed->path = $path;
            $resourceModuleDetailed->social_link_id = $social_link_id;
            $resourceModuleDetailed->save();

            return $resourceModuleDetailed;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileUpload($request, $resource_module_id)
    {
        try {
            if (isset($request->file_upload) && !empty($request->file_upload)) {
                foreach ($request->file_upload as $file_upload) {
                    if (false !== mb_strpos($file_upload->getMimeType(), 'image')) {
                        $file_type = config('constants.file_type.image');
                        $uploaded_file_path = FileUploadHelper::uploadImageToS3($file_upload, 'resource_file');
                    } else {
                        $file_type = (mb_strpos($file_upload->getMimeType(), 'video') !== false) ? config('constants.file_type.video') : config('constants.file_type.document');
                        $uploaded_file_path = FileUploadHelper::UploadVideoDocToS3($file_upload, 'resource_file');
                    }

                    if ($uploaded_file_path == false) {
                        return false;
                    }
                    $storeData = self::insertRecords($resource_module_id, $file_upload->getClientOriginalName(), $file_type, $uploaded_file_path, null);
                    if (!$storeData) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteResourceModuleDetail($resource_module_id)
    {
        try {
            ResourceModuleDetail::where('resource_module_id', $resource_module_id)->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteResourceModuleMedia($request, $resource_module_id)
    {
        try {
            switch ($request->type) {
                case 'document':
                    $type = config('constants.resource_module_type.document');
                    break;
                case 'video':
                    $type = config('constants.resource_module_type.video');
                    break;
                case 'audio':
                    $type = config('constants.resource_module_type.audio');
                    break;
                case 'embedded_video':
                    $type = config('constants.resource_module_type.embedded_video');
                    break;
                case 'embedded_audio':
                    $type = config('constants.resource_module_type.embedded_audio');
                    break;
                case 'url':
                    $type = config('constants.resource_module_type.url');
                    break;
                case 'image':
                    $type = config('constants.resource_module_type.image');
                    break;
                case 'embedded_cover_video':
                    $type = config('constants.resource_module_type.Embedded_Cover_Video');
                    break;
                default:
                    $type = config('constants.resource_module_type.image');
                    break;
            }
            ResourceModuleDetail::where([
                'id'                 => $request->media_id,
                'resource_module_id' => $resource_module_id,
                'type'               => $type,
            ])->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addLinks($request, $resource_module_id)
    {
        try {
            foreach ($request->links as  $value) {
                $type = config('constants.resource_module_type.url');
                $resourceModuleDetailed = self::insertRecords($resource_module_id, $value['title'], $type, $value['path'], $value['social_link_id']);
                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addEmbeddedMedia($request, $resource_module_id)
    {
        try {
            foreach ($request->embed_media as $key => $value) {
                switch ($value['type']) {
                    case 'embedded_video':
                        $type = config('constants.resource_module_type.embedded_video');
                        $title = $value['type'];
                        break;
                    case 'embedded_audio':
                        $type = config('constants.resource_module_type.embedded_audio');
                        $title = $value['type'];
                        break;
                    default:
                        $type = '';
                }
                $resourceModuleDetailed = self::insertRecords($resource_module_id, $title, $type, $value['path'], null);
                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createResourceModuleDetailsAI($request, $resource_module_id)
    {
        try {
            // Log the entire request data for inspection
            Log::info('Logging entire request:', (array) $request);

            // If $request is an instance of Request, convert to array, else assume it's already an array
            $requestData = $request instanceof Request ? $request->all() : $request;

            // Log the converted/request data to ensure it's in expected format
            Log::info('Converted/Original request data:', $requestData);

            if (is_array($requestData)) {
                foreach ($requestData as $key => $item) {
                    // Log each key to verify we're iterating correctly
                    Log::info("Processing key: {$key}");

                    // Check if the current item is an array and has a numeric key
                    if (is_numeric($key) && is_array($item)) {
                        // Log the item to be processed
                        Log::info("Current item:", $item);

                        // Proceed with processing
                        $resourceDetail = new ResourceModuleDetail([
                            'title' => $item['title'], // Set the title from the item
                            'path' => $item['url'], // Set the path (URL) from the item
                            'resource_module_id' => $resource_module_id, // Use the provided $resource_module_id
                        ]);

                        if (isset($item['embedHTML']) && !empty($item['embedHTML'])) {
                            $resourceDetail->type = "3"; // Set type to 3 if embedHTML exists
                        }

                        // Log the model instance before saving
                        Log::info("ResourceModuleDetail instance:", $resourceDetail->getAttributes());

                        $resourceDetail->save(); // Insert the record into the database
                    }
                }
            } else {
                // Log an error if the request data is not an array
                Log::error('Request data is not an array.');
                return false;
            }

            return true; // Return true if everything was processed successfully
        } catch (Exception $e) {
            Log::error('Exception caught in createResourceModuleDetailsAI:', ['message' => $e->getMessage()]);
            return false; // Return false in case of any errors
        }
    }
}
