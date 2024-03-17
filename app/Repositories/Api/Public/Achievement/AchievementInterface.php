<?php

namespace App\Repositories\Api\Public\Achievement;

interface AchievementInterface
{
    public function getList($request);

    public function getAchievementBasedOnCertificateNumber($certificate_id);

    public function downloadCertificate($certificate_id, $format);

    public function getAchievementList($userId, $request);
}
