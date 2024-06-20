<?php

namespace Database\Seeders;

use App\Models\BusinessChallengeTackling;
use Illuminate\Database\Seeder;

class BusinessChallengeTacklingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessChallenges = [
            [
                'title'         => 'Sales & Marketing: Drives revenue and is often the face of the company to customers.',
                'fr_CA_title'   => "Ventes et marketing : génère des revenus et représente souvent le visage de l'entreprise auprès des clients.",
            ],
            [
                'title'         => 'Human Resources (HR): Manages talent, which is the backbone of any company.',
                'fr_CA_title'   => "Ressources humaines (RH) : gère les talents, qui constituent l'épine dorsale de toute entreprise.",
            ],
            [
                'title'         => 'IT Management: Especially in the digital age, IT is pivotal for operations, communication, and innovation.',
                'fr_CA_title'   => "Gestion informatique : particulièrement à l'ère du numérique, l'informatique est essentielle aux opérations, à la communication et à l'innovation.",
            ],
            [
                'title'         => 'Customer Service: Directly impacts customer retention and satisfaction.',
                'fr_CA_title'   => 'Service client : impact direct sur la fidélisation et la satisfaction des clients.',
            ],
            [
                'title'         => 'Research & Development: Fuels innovation and helps companies stay competitive.',
                'fr_CA_title'   => 'Recherche et développement : alimente l’innovation et aide les entreprises à rester compétitives.',
            ],
            [
                'title'         => 'Business Development: Expands the business’s reach into new markets and opportunities.',
                'fr_CA_title'   => 'Développement commercial : étend la portée de l’entreprise à de nouveaux marchés et opportunités.',
            ],
            [
                'title'         => 'Sustainability & Environmental Management: Increasingly important due to global environmental concerns.',
                'fr_CA_title'   => 'Durabilité et gestion environnementale : de plus en plus importante en raison des préoccupations environnementales mondiales.',
            ],
        ];
        foreach ($businessChallenges as $businessChallenge) {
            BusinessChallengeTackling::updateOrCreate([
                'title'       => $businessChallenge['title'],
                'fr_CA_title' => $businessChallenge['fr_CA_title'],
            ], [
                'title'       => $businessChallenge['title'],
                'fr_CA_title' => $businessChallenge['fr_CA_title'],
            ]);
        }
    }
}
