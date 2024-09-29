<?php

namespace Database\Seeders;

use App\Models\ChallengeAnnouncementRecipient;
use Illuminate\Database\Seeder;

class ChallengeAnnouncementRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $challenge_announcement_recipients = [
            [
                'title'      => 'Invited Challenge Participants',
                'fr_CA_title'=> 'Participants invités au défi',
            ],
            [
                'title'      => 'Challenge Savers',
                'fr_CA_title'=> 'Épargnants de défis',
            ],
            [
                'title'      => 'Challenge Achievement Winners',
                'fr_CA_title'=> 'Gagnants des réalisations du défi',
            ],
            [
                'title'      => 'Auto-invite Accept Participants',
                'fr_CA_title'=> 'Inviter automatiquement Accepter les participants',
            ],
            [
                'title'      => 'Associated Lab Users',
                'fr_CA_title'=> 'Utilisateurs du laboratoire associés',
            ],
            [
                'title'      => 'Associated Project Users',
                'fr_CA_title'=> 'Utilisateurs du projet associés',
            ],
            [
                'title'      => 'Submitted Project Users',
                'fr_CA_title'=> 'Utilisateurs du projet soumis',
            ],
            [
                'title'      => 'Participant Achievement Winners',
                'fr_CA_title'=> 'Gagnants des réalisations des participants',
            ],
        ];
        foreach ($challenge_announcement_recipients as $challenge_announcement_recipient) {
            ChallengeAnnouncementRecipient::updateOrCreate([
                'title'      => $challenge_announcement_recipient['title'],
                'fr_CA_title'=> $challenge_announcement_recipient['fr_CA_title'],
            ], [
                'title'      => $challenge_announcement_recipient['title'],
                'fr_CA_title'=> $challenge_announcement_recipient['fr_CA_title'],
            ]);
        }
    }
}
