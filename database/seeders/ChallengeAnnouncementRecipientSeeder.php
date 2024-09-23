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
                'title'      => 'Invited Challenge participant',
                'fr_CA_title'=> 'Participant invité au défi',
            ],
            [
                'title'      => 'Challenge savers',
                'fr_CA_title'=> 'Épargnants de défis',
            ],
            [
                'title'      => 'Challenge Achievement Winners',
                'fr_CA_title'=> 'Gagnants des réalisations du défi',
            ],
            [
                'title'      => 'Auto-invite accept participants',
                'fr_CA_title'=> 'Inviter automatiquement accepter les participants',
            ],
            [
                'title'      => 'Associated Lab users',
                'fr_CA_title'=> 'Utilisateurs du laboratoire associés',
            ],
            [
                'title'      => 'Associated project users',
                'fr_CA_title'=> 'Utilisateurs du projet associés',
            ],
            [
                'title'      => 'Submitted project users',
                'fr_CA_title'=> 'Utilisateurs du projet soumis',
            ],
            [
                'title'      => 'Participant trophy winners',
                'fr_CA_title'=> 'Gagnants du trophée des participants',
            ],
            [
                'title'      => 'Incentive Trophy winners',
                'fr_CA_title'=> 'Les lauréats du Trophée Incentive',
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
