<?php

namespace App\Services;
use App\Models\Favorite;
class FavoriteService
{
    public function create($request){
        try {
            switch($request->reference_type){
                case "lab":
                $reference_type=config('constants.favorites_refence_type.lab');
                break;
                case "project":
                $reference_type=config('constants.favorites_refence_type.project');
                break;
                case "user":
                $reference_type=config('constants.favorites_refence_type.user');
                break;
                case "challange":
                $reference_type=config('constants.favorites_refence_type.challange');
                break;
                case "challenge-group":
                $reference_type=config('constants.favorites_refence_type.challenge-group');
                break;
                case "lab-group":
                $reference_type=config('constants.favorites_refence_type.lab-group');
                break;
                default:
                $reference_type=config('constants.favorites_refence_type.lab');
                break;
            }
            switch($request->is_like){
                case "yes":
                    $is_like=config('constants.favorites_is_like.yes');
                break;
                case "no":
                $is_like=config('constants.favorites_is_like.no');
                break;
                default:
                $is_like=config('constants.favorites_is_like.no');
                break;
            }
            $favourite=new Favorite;
            $favourite->reference_id=$request->reference_id;
            $favourite->reference_type=$reference_type;
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
            switch($request->is_like){
                case "yes":
                    $is_like=config('constants.favorites_is_like.yes');
                break;
                case "no":
                $is_like=config('constants.favorites_is_like.no');
                break;
                default:
                $is_like=config('constants.favorites_is_like.no');
                break;
            }
        
            $isAlreadyLikedOrNotLiked=Favorite::Select('id')->where(
                [
                ["reference_id","=",$request->reference_id],
                ["reference_type","=",$refence_type],
                ["is_like","=",$is_like],
                ["user_id","=",auth()->user()->id],
                ])->first();

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