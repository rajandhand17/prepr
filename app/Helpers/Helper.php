<?php
namespace App\Helpers;

use Exception;
use App\Models\MemberManagement;
use App\Models\Lab;
use App\Models\Challange;
use App\Models\Organisation;
use App\Models\Language;
use App\Models\ChallengePrice;
use App\Models\ChallengePitche;
Class Helper{
    
    /* -----------------------------------------------------------------------------------------
      @Description: checks the difference between two pitch values
      @input: old_pitch_values, and new_pitch_values
      @Output: the indeces that are different in the pitches
      -------------------------------------------------------------------------------------------- */
    public static function syncUserMembersForLabTemplates($user, $lab_id)
    {
        $labDetails = Lab::find($lab_id);
        if ($labDetails) {
            $checkInvite = MemberManagement::where(['module_id' => $lab_id, 'email' => $user->email, 'invite_status' => 'pending', 'module_type' => 'lab'])->first();
            if (empty($checkInvite)) {
                $inviteData['invite_type'] = 'autocreate';
                $inviteData['invitee_id'] = $user->id;
                $inviteData['email'] = $user->email;
                $inviteData['module_id'] = (int) $lab_id;
                $inviteData['module_type'] = 'lab';
                $inviteData['inviter_id'] = (int) $labDetails->user_id;
                $inviteData['auto_invite_status'] = 'other';
                $inviteData['invite_status'] = 'accepted';
                $inviteData['email_status'] = 'other';
                $inviteData['assign_role'] = 'user';
                $inviteData['is_auto_created'] = 1;
                MemberManagement::insert($inviteData);
            }
        }
    }

    public static function syncUserMembersForChallengeTemplates($user_id, $user_email, $challenge_id)
    {
        $challengeDetails = Challange::find($challenge_id);
        if ($challengeDetails) {
            $checkInvite = MemberManagement::where(['module_id' => $challenge_id, 'email' => $user_email, 'invite_status' => 'pending', 'module_type' => 'challenge'])->first();
            if (empty($checkInvite)) {
                $inviteData['invite_type'] = 'autocreate';
                $inviteData['invitee_id'] = $user_id;
                $inviteData['email'] = $user_email;
                $inviteData['module_id'] = (int) $challenge_id;
                $inviteData['module_type'] = 'challenge';
                $inviteData['inviter_id'] = (int) $challengeDetails->user_id;
                $inviteData['auto_invite_status'] = 'other';
                $inviteData['invite_status'] = 'accepted';
                $inviteData['email_status'] = 'other';
                $inviteData['assign_role'] = 'user';
                $inviteData['is_auto_created'] = 1;
                MemberManagement::insert($inviteData);
            }
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description:  This Function can get language iso besed on email.
    @Input: email
    @Output: return language iso
    -------------------------------------------------------------------------------------------- */
    public static function getLanguageIso($email)
    {
        $userData = User::select('language_id')->where(['email' => trim($email)])->first();
        if (!empty($userData)) {
            $language = Language::select('lang_iso')->where('id', $userData->language_id)->first();
            if (!empty($language)) {
                return $language['lang_iso'];
            } else {
                return 'en';
            }
        } else {
            return 'en';
        }
    }

    
    /* -----------------------------------------------------------------------------------------
      @Description: Function for creating challenge for free challenge manager
      @input: user id, organisation id
      @Output:
      -------------------------------------------------------------------------------------------- */

      public static function freeChallengemanagerSync($user_id, $org_id, $templateId)
      {
          if ($org_id == '') {
              $organisation = Organisation::first();
              $org_id = $organisation->id;
          }
          $organisation = Organisation::where('id', $org_id)->first();
          $challenge = Challange::find($templateId);
  
          if ($challenge) {
              $cloneChallenge= $challenge->replicate();
              $cloneChallenge->title= $organisation->name."_".$challenge->title;
              $cloneChallenge->slug= str_slug($organisation->name).'_'.$challenge->slug;
              $cloneChallenge->user_id= $organisation->user_id;
              $cloneChallenge->organisation= $organisation->id;
              $cloneChallenge->privacy= 'private';
              $cloneChallenge->is_completed= '0';
              $cloneChallenge->is_auto_created= '1';
              $cloneChallenge->save();
  
              //clone participant trophy
              $getParticipants=ChallengePrice::where('challenge_id', $challenge->id)->get();
  
              if ($getParticipants) {
                  foreach ($getParticipants as $singleParticipant) {
                      ChallengePrice::create([
                          'challenge_id' => $cloneChallenge->id,
                          'type'      => $singleParticipant->type,
                          'name'      => $singleParticipant->name,
                          'prize'     => $singleParticipant->prize,
                          'points'    => $singleParticipant->points,
                          'trophy'    => $singleParticipant->trophy,
                      ]);
                  }
              } else {
                  $challenge_participant = ChallengePrice::create(['challenge_id' => $cloneChallenge->id, 'type' => 'participation','name'=>'Participant','prize'=>'Participation prize','points'=>100,'trophy'=>'front/img/ic_trophies.png']);
              }
  
              //clone incentives trophy
              $getIncentives=ChallengePrice::where('challenge_id', $challenge->id)->get();
  
              if ($getIncentives) {
                  foreach ($getIncentives as $singleIncentive) {
                      ChallengePrice::create([
                          'challenge_id' => $cloneChallenge->id,
                          'type'      => $singleParticipant->type,
                          'name'      => $singleParticipant->name,
                          'prize'     => $singleParticipant->prize,
                          'points'    => $singleParticipant->points,
                          'trophy'    => $singleParticipant->trophy,
                      ]);
                  }
              } else {
                  $challenge_incentive = ChallengePrice::create(['challenge_id' => $cloneChallenge->id, 'type' => 'incentive','name'=>'1st Place','prize'=>'Incentive prize','points'=>100,'trophy'=>'front/img/ic_trophies.png']);
              }
  
              //clone pitch data
              $getPitch=ChallengePitche::where('challenge_id', $challenge->id)->get();
  
              if ($getPitch) {
                  foreach ($getPitch as $singlePitch) {
                      ChallengePitche::create(['challenge_id' => $cloneChallenge->id, 'pitch_template_id' => $singlePitch->pitch_template_id]);
                  }
              }
  
  
              //clone Assessment data
              $getAssessment=\App\Models\ChallengeAssessment::where('challenge_id', $challenge->id)->get();
  
              if ($getAssessment) {
                  foreach ($getAssessment as $singleAssessment) {
                      if ($singleAssessment->assessment_type == 'open' || $singleAssessment->assessment_type == 'none') {
                          \App\Models\ChallengeAssessment::create([
                              'challenge_id' => $cloneChallenge->id,
                              'assessment_type' => $singleAssessment->assessment_type,
                              'guidline' => $singleAssessment->guidline,
                              'attachment' => $singleAssessment->attachment,
                              'visibility' => $singleAssessment->visibility,
                              'members' => null,
                          ]);
                      }
                  }
              }
  
              //clone challenge tags data
              $getTags=\App\Models\ChallengeTag::where('challange_id', $challenge->id)->get();
  
              if ($getTags) {
                  foreach ($getTags as $singleTag) {
                      \App\Models\ChallengeTag::create(['challange_id' => $cloneChallenge->id, 'tag' => $singleTag->tag, 'user_id' => $cloneChallenge->user_id]);
                  }
              }
  
              //clone challenge resources data
              $getLabRes = \App\Models\ResourceData::where('admin_challenge_id', $challenge->id)->where('resource_datas_id', '!=', 0)->get();
  
              if ($getLabRes) {
                  foreach ($getLabRes as $singleLabRes) {
                      \App\Models\ResourceData::create([
                          'admin_challenge_id' =>  $cloneChallenge->id,
                          'resource_datas_id' =>  $singleLabRes->resource_datas_id,
                      ]);
                  }
              }
  
              //clone challenge resourcecollection data
              $getResources=\App\Models\ResourceCollectionForLabAndChallenge::where('challenge_id', $challenge->id)->get();
  
              if ($getResources) {
                  foreach ($getResources as $singleResource) {
                      \App\Models\ResourceCollectionForLabAndChallenge::create([
                          'user_id' =>  $cloneChallenge->user_id,
                          'challenge_id' =>  $cloneChallenge->id,
                          'resource_collection_id' => $singleResource->resource_collection_id,
                      ]);
                  }
              }
  
              //clone announcements
              if ($challenge->dates='flexible') {
                  $getCustomDates= \App\Models\ChallangeCustomTime::where('challenge_id', $challenge->id)->get();
  
                  if ($getCustomDates) {
                      foreach ($getCustomDates as $singleCustomDate) {
                          $id = \App\Models\ChallangeCustomTime::create([
                              'challenge_id' => $cloneChallenge->id,
                              'title' => $singleCustomDate->title,
                              'date' => $singleCustomDate->date,
                              'description' => $singleCustomDate->description,
                              'scheduleAnnouncement' => $singleCustomDate->scheduleAnnouncement,
                              'customDateNumber' => $singleCustomDate->customDateNumber,
                              'customDateDuration' => $singleCustomDate->customDateDuration,
                          ])->id;
                          $customDateId = $singleCustomDate->id;
                          $getAnnouncements= \App\Models\FlexibleAnnouncement::where(
                              [
                                  'challenge_id' => $challenge->id,
                                  'customDateId' => $customDateId
                              ]
                          )->first();
                          if ($getAnnouncements) {
  
  //                                        foreach ($getAnnouncements as $singleAnnouncement) {
                              $singleNewAnnouncement = \App\Models\FlexibleAnnouncement::create([
                                  'sent_status'               => $getAnnouncements->sent_status,
                                  'title'                     => $getAnnouncements->title,
                                  'body'                      => $getAnnouncements->body,
                                  'schedule_status'           => $getAnnouncements->schedule_status,
                                  'is_completed'              => $getAnnouncements->is_completed,
                                  'customDateId'              => $id,
                                  'challenge_id'              => $cloneChallenge->id,
                                  'user_id'                   => $cloneChallenge->user_id,
                                  'announcementNumber'        => $getAnnouncements->announcementNumber,
                                  'announcementSchedule'      => $getAnnouncements->announcementSchedule,
                                  'announcementScheduleTime'  => $getAnnouncements->announcementScheduleTime,
                              ]);
  //                                        }
                          }
                      }
                  }
              } elseif ($challenge->dates='restricted') {
                  $getAnnouncements= \App\Models\ChallengeAnnouncement::where('challenge_id', $challenge->id)->get();
                  if ($getAnnouncements) {
                      foreach ($getAnnouncements as $singleAnnouncement) {
                          \App\Models\ChallengeAnnouncement::create([
                              'sent_status'               => $singleAnnouncement->sent_status,
                              'title'                     => $singleAnnouncement->title,
                              'body'                      => $singleAnnouncement->body,
                              'schedule_status'           => $singleAnnouncement->schedule_status,
                              'is_completed'              => 0,
                              'is_send'                   => 0,
                              'customDateId'              => null,
                              'challenge_id'              => $cloneChallenge->id,
                              'user_id'                   => $cloneChallenge->user_id,
                              'announcementNumber'        => $singleAnnouncement->announcementNumber,
                              'announcementSchedule'      => $singleAnnouncement->announcementSchedule,
                              'date'                      => $singleAnnouncement->date,
                              'time'                      => $singleAnnouncement->time,
                              'recipients'                => $singleAnnouncement->recipients,
                          ]);
                      }
                  }
              }
          }
      }
}