<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\comment;
use Illuminate\Support\Facades\Schema;

class CommentService
{
    public function addComment($request){
        try {
            $addComment=new comment();
            $addComment->user_id = $request->user_id;
            $addComment->module_id = $request->module_id;
            $addComment->module_type=$request->module_type;
            $addComment->comment=$request->comment;
            $addComment->attachments=$request->attachments;
            $addComment->comment_id=$request->comment_id;
            $addComment->save();
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    public function deleteComment($request){
        try{
            $deleteComment=comment::find($request->id);
            $deleteComment->delete();
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
}
