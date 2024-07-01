<?php

namespace App\Traits\Maestro\EmailTemplate;

use App\Models\EmailTemplate;
use App\Services\Maestro\EmailTemplate\EmailTemplateService;
use Exception;

trait EmailTemplateTrait
{
    private function createEmailTemplate($request)
    {
        try {
            if(EmailTemplateService::createEmailTemplate($request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateEmailTemplateById($id,$request)
    {
        try {
            if(EmailTemplateService::updateEmailTemplateById($id,$request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function deleteEmailTemplateById($id)
    {
        try {
            if(EmailTemplateService::deleteEmailTemplate($id)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function getEmailTemplates()
    {
        try {
            $orgs = EmailTemplateService::getEmailTemplates();
            if($orgs){
                return $orgs;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
