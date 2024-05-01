<?php

namespace App\Repositories\Api\Public\Scorm;

use App\Models\Scorm;
use App\Models\User;

interface ScormInterface
{
    /**
     * @param string $uuid
     * @param User   $scormUser
     *
     * @return mixed
     */
    public function getScorm(string $uuid, User $scormUser);

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function generateScormProxy(string $url);

    /**
     * @param Scorm $scorm
     *
     * @return mixed
     */
    public function generateScormPlayerUrl(Scorm $scorm);
}
