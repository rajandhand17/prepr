<?php

namespace App\Http\Controllers\LeaderBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaderBoardController extends Controller
{
    public function __construct(){

    }

    public function index(){
        try{

        }catch(\Exception $e){
            return $this->sendResponse(__('responses.send_error'));
        }
    }
}
