<?php

namespace App\Services;

use App\Models\labChallenges;
use DB;
class LabChallengesService
{
    public function storeChallengeId($request,$lab){
    try{
        $selectedChallenge=$request->challenge_id;
        foreach ($selectedChallenge as $key => $challenge_id) {
            $labchllebges= LabChallenges::create([
            'lab_id' =>  $lab->id,
            'challenge_id' => $challenge_id,
            'sequence_no' => $key+1,
            ]);
        }
    } catch (\Exception $e) {
        return false;
    }
    }

    public function storeChallengePathId($request,$lab){
        try{
            $SelectedPaths=$request->challenge_path_id;
            foreach ($SelectedPaths as $key => $path_id) {
                LabChallenges::create([
                    'lab_id' =>  $lab->id,
                    'challenge_path_id' => $path_id,
                    'sequence_no' => $key+1,
                ]);
            }
        } catch (\Exception $e) {
            return false;
        }
        }
}