<?php

namespace App\Http\Controllers\Api\EmailSummary;

use App\Helpers\EmailSummaryHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use Exception;

class EmailSummaryController extends AppBaseController
{
    public static function sendEmailSummary($userData, $summeryType)
    {
        try {
            // Check user language preference or default set to English ('en' ).
            $languageISO = ($userData->preferred_language != null) ? $userData->preferred_language : 'en';
            // Check user preferred language is available in system if not then return en from below function.
            $summaryLanguage = EmailSummaryHelper::fetchLangaugeISO($languageISO);

            // Case for summary type for email subscriptins.
            $commonSummeryContent = EmailSummaryHelper::summaryLanguageContent($summaryLanguage);
            $challengeRecommendSummeryContent = EmailSummaryHelper::challengeRecommendationEmailSubscriptionLanguageContent($userData->language);
            switch ($summeryType) {
                case 'monthly':
                    if ($userData['userSetting']->email_subscription_network_summary == '1') {
                        $networkSummary = EmailSummaryHelper::networkEmailSummary($userData, $summeryType, $commonSummeryContent);
                    }

                    if ($userData['userSetting']->email_subscription_lab_summary == '1') {
                        $labSummary = EmailSummaryHelper::labEmailSubscription($userData, $summeryType, $commonSummeryContent);
                    }

                    if ($userData['userSetting']->email_subscription_challenge_summary == '1') {
                        $challengeSummery = EmailSummaryHelper::challengeEmailSubscription($userData, $summeryType, $commonSummeryContent);
                    }

                    if ($userData['userSetting']->challenge_recommends == '1') {
                        // $challengeSummery = EmailSummaryHelper::challengeRecommendationEmailSubscription($userData, $summeryType, $challengeRecommendSummeryContent);
                    }
                    break;
                case 'weekly':
                    if ($userData['userSetting']->email_subscription_network_summary == '1') {
                        $networkSummary = EmailSummaryHelper::networkEmailSummary($userData, $summeryType, $commonSummeryContent);
                    }

                    if ($userData['userSetting']->email_subscription_lab_summary == '1') {
                        $labSummary = EmailSummaryHelper::labEmailSubscription($userData, $summeryType, $commonSummeryContent);
                    }

                    if ($userData['userSetting']->email_subscription_challenge_summary == '1') {
                        $challengeSummery = EmailSummaryHelper::challengeEmailSubscription($userData, $summeryType, $commonSummeryContent);
                    }

                    if ($userData['userSetting']->challenge_recommends == '1') {
                        // $challengeSummery = EmailSummaryHelper::challengeRecommendationEmailSubscription($userData, $summeryType, $challengeRecommendSummeryContent);
                    }
                    break;
            }

        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
