<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Support\Facades\Validator;
use Schema;

class EmailTemplateService
{
    public static function updateEmailTemplateById($id, $request)
    {
        try {
            $template = EmailTemplate::find($id);
            $input = $request->all();
            $Validate = Validator::make($request->all(), [
                'subject'      => 'required|max:255',
                'body_content' => 'required',
            ]);

            if ($Validate->fails()) {
                return redirect()->back()->withErrors($Validate)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('email_templates', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if (!empty($insertArray)) {
                EmailTemplate::where('id', $id)->update($insertArray);

                return true;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteEmailTemplate($id)
    {
        try {
            $EmailTemplate = EmailTemplate::find($id);

            if ($EmailTemplate) {
                return $EmailTemplate->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createEmailTemplate($request)
    {
        try {
            $input = $request->all();
            $Validate = Validator::make($request->all(), [
                'subject'      => 'required|max:255',
                'body_content' => 'required',
            ]);

            if ($Validate->fails()) {
                return redirect()->back()->withErrors($Validate)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('email_templates', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if (!empty($insertArray)) {
                EmailTemplate::create($insertArray);

                return true;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getEmailTemplates()
    {
        try {
            return EmailTemplate::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getEmailTemplatesById($id)
    {
        try {
            $EmailTemplate = EmailTemplate::find($id);

            if ($EmailTemplate) {
                return $EmailTemplate;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
