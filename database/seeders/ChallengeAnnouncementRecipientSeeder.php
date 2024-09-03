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
                'title'      => 'Challenge followers',
                'fr_CA_title'=> 'Défiez les abonnés',
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
                'title'      => 'Participant achievement winners',
                'fr_CA_title'=> 'Gagnants des réalisations des participants',
            ],
            [
                'title'      => 'Challenge achievement winners',
                'fr_CA_title'=> 'Gagnants des réalisations du défi',
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
