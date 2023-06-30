<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\LabAddress;
use DB;
class LabAddressService
{
   public function store($request,$lab){
          try {
            DB::beginTransaction();
            $labaddress=new LabAddress();
            $labaddress->lab_id =$lab->id;
            $labaddress->latitute=$request->latitute;
            $labaddress->longitude=$request->longitude;
            $labaddress->address=$request->address;
            $labaddress->city=$request->city;
            $labaddress->country=$request->country;
            if($labaddress->save()){
                DB::commit();
                return true;
            }
            DB::rollback();
            return false;
          } catch (\Exception $e) {
            DB::rollback();
            return false;
          }
   }
}