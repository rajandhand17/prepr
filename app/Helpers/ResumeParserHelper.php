<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ResumeParserHelper
{
    public static function getResumeData($request)
    {
        try {
            $resumeFile = $request->file('resume');
            $resumePath = 'uploads/personal_files/'.auth()->user()->id.'_'.$resumeFile->getClientOriginalName();
            Storage::disk('s3')->put($resumePath, file_get_contents($resumeFile));
            $apiResponse = Http::withHeaders([
                'Accept'    => 'application/json',
                'X-API-Key' => config('resume'),
            ])->attach(
                'file_name',
                file_get_contents($resumeFile->path()),
                $resumeFile->getClientOriginalName(),
                ['Content-Type' => $resumeFile->getClientMimeType()]
            )->post('https://api.superparser.com/parse');
            if ($apiResponse->successful()) {
                return  $apiResponse->json();
            }
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
