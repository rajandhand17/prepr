<?php

namespace App\Repositories\Api\Public\Profile;

interface ProfileInterface
{
    public function getCountriesList($language,$search);
}
