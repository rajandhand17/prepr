<?php

namespace App\Repositories\Api\Lab;

Interface LabInterface{
  public function list($request);
  public function create($request);
  public function draft($request);
  public function edit($request);
  public function delete($request);
  public function labDetail($request);
  public function checkLabSlug($request);
  public function checkLabName($request);
  public function getSkills($request);
  public function getTags($request);
  public function getLabPrograms($request);
  public function genrateReportExcel($request);
  public function genrateReportPdf($request);
  public function likeUnlike($request);
  public function followUnfollow($request);
  public function joinLab($request);
  public function share($request);
  public function view($request);
}

?>