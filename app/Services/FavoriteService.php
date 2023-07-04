<?php

namespace App\Services;
use App\Models\Favorite;
class FavoriteService
{
    public function likeUnlikeLab($request){
        try {
            $favourite=new Favorite;
            $favourite->refence_id=$request->refence_id;
            switch($request->refence_type){
                case "lab":
                $refence_type=config('constants.favorites_refence_type.lab');
                break;
                case "project":
                $refence_type=config('constants.favorites_refence_type.project');
                break;
                case "user":
                $refence_type=config('constants.favorites_refence_type.user');
                break;
                case "challange":
                $refence_type=config('constants.favorites_refence_type.challange');
                break;
                case "challenge-group":
                $refence_type=config('constants.favorites_refence_type.challenge-group');
                break;
                case "lab-group":
                $refence_type=config('constants.favorites_refence_type.lab-group');
                break;
                default:
                $refence_type=config('constants.favorites_refence_type.lab');
                break;
            }
            $favourite->refence_type=$refence_type;

            switch($request->is_like){
                case "like":
                    $is_like=config('constants.favorites_is_like.like');
                break;
                case "unlike":
                $is_like=config('constants.favorites_is_like.unlike');
                break;
                default:
                $is_like=config('constants.favorites_is_like.unlike');
                break;
            }
            $favourite->is_like=$is_like;
            $favourite->user_id=auth()->user()->id;
            if($favourite->save()){
                return $favourite;
            }
            return false;
        } catch (\Exception $e){
            return false;
        }
    }

    public function isAlreadyLikedOrNotLiked($request){
        try {
            $isAlreadyLikedOrNotLiked=Favorite::Select('id')->where(["refence_id"=>$request->reference_id,"refence_type"=>$request->refence_type,"is_like"=>$request->is_like,"user_id"=>auth()->user()->id])->first();
            if($isAlreadyLikedOrNotLiked){
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e){
            return false;
        }
    }

}