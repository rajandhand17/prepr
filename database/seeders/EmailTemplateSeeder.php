<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emailTemplates = [
            [
                'template_type' => "0",
                'module_type' => "0",
                'subject' => "Invitation to join Organization",
                'fr_CA_subject' => "Invitation à rejoindre l'organisation",
                'body_content' => 'You have been invited to the organization $organization_name by $user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the organization once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité à l\'organisation $organization_name par $user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté à l\'organisation une fois connecté.',
            ]
        ];

        foreach ($emailTemplates as $emailTemplate){
            EmailTemplate::updateOrCreate([
                'template_type' => "0",
                'module_type' => "0",
            ],[
                'subject' => "Invitation to join Organization",
                'fr_CA_subject' => "Invitation à rejoindre l'organisation",
                'body_content' => 'You have been invited to the organization $organization_name by $user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the organization once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité à l\'organisation $organization_name par $user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté à l\'organisation une fois connecté.',
            ]);
        }
    }
}
