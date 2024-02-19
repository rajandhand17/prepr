<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\comment;
use App\Models\Discussion;
use App\Models\CommentSocialActivity;
use Composer\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiscussionService
{
    public function addComment($component,$request,$getComponentId){
        try {
            $attachmentPath=null;
            if($request->file('attachments') && $request->file('attachments') !== null){
                $attachments = $request->file('attachments');
                $attachmentPath = 'uploads/comments/'.auth()->user()->id.Str::random(40).'.'.$attachments->extension();
                Storage::disk('s3')->put($attachmentPath, file_get_contents($attachments));
            }
            $addComment=new comment();
            $addComment->user_id     = auth()->user()->id;
            $addComment->module_id   = $getComponentId;
            $addComment->module_type = config('constants.discussion_module_type.'.$component);
            $addComment->comments     = $request->comment ? $request->comment : null;
            $addComment->attachment = $attachmentPath;
            $addComment->comment_id  = isset($request->comment_id) ? $request->comment_id: null ;
            $addComment->save();
            return $addComment;
        }catch(\Exception $e){
            return false;
        }
    }

}
