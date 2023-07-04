<?php

namespace App\Repositories\Api\Lab;

interface LabInterface
{
  public function createLab($request,$upload_profile_image,$upload_acheivements_image);
  public function uploadCoverImage($image);
  public function getLabDetails($slug);
  public function checkSlug($slug);

  public function checkNameExistsOrNot($slug);


}
