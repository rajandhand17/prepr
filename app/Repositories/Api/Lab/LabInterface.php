<?php

namespace App\Repositories\Api\Lab;

interface LabInterface
{
  public function store($component,$request,$upload_profile_image,$upload_acheivements_image);
  public function uploadCoverImage($image);
  public function getLabDetailed($slug);
  public function checkLabSlug($slug);

  public function checkLabNameExistsOrNot($slug);

  public function getSkills($request);

}
