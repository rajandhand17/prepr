<?php

namespace App\Traits\Maestro\EmailTemplate;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\EmailTemplateService;
use Exception;

trait EmailTemplateTrait
{
    private function createEmailTemplate($request)
    {
        try {
            if (EmailTemplateService::createEmailTemplate($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function updateEmailTemplateById($id, $request)
    {
        try {
            if (EmailTemplateService::updateEmailTemplateById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function deleteEmailTemplateById($id)
    {
        try {
            if (EmailTemplateService::deleteEmailTemplate($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getEmailTemplates()
    {
        try {
            $emailTemplates = EmailTemplateService::getEmailTemplates();
            if ($emailTemplates) {
                return $emailTemplates;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getEmailTemplatesById($id)
    {
        try {
            $emailTemplates = EmailTemplateService::getEmailTemplatesById($id);
            if ($emailTemplates) {
                return $emailTemplates;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
