<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\comment;
use Composer\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentService
{
    public function index($component,$commentId){
        try {
            $moduleType=Config('constants.discussion_module_type.'.$component);
            $getComments = Comment::whereNull('comment_id')
                ->where('module_id', $commentId)
                ->where('module_type',$moduleType)
                ->get();
            if($getComments){
                return $getComments;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }
    public function addComment($component,$request){
        try {
            $attachmentPath=null;
            if($request->file('attachments') && $request->file('attachments') !== null){
                $attachments = $request->file('attachments');
                $attachmentPath = 'uploads/comments/'.auth()->user()->id.Str::random(40).'.'.$attachments->extension();
                Storage::disk('s3')->put($attachmentPath, file_get_contents($attachments));
            }
            $addComment=new comment();
            $addComment->user_id     = auth()->user()->id;
            $addComment->module_id   = $request->module_id;
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

    public function deleteComment($commentId){
        try{
            $deleteComment=comment::find($commentId);
            $deleteComment->delete();
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
}
