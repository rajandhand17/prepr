<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
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
                'template_type'      => '0',
                'module_type'        => '0',
                'subject'            => 'Invitation to join Organization',
                'fr_CA_subject'      => "Invitation à rejoindre l'organisation",
                'body_content'       => 'You have been invited to the organization component_title by user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the organization once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité à l\'organisation component_title par user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté à l\'organisation une fois connecté.',
            ],
            [
                'template_type'      => '0',
                'module_type'        => '1',
                'subject'            => 'Invitation to join Lab',
                'fr_CA_subject'      => 'Invitation à rejoindre le laboratoire',
                'body_content'       => 'You have been invited to the lab component_title by user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the Lab once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité au laboratoire component_title par user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté à Laboratoire une fois connecté.',
            ],
            [
                'template_type'      => '0',
                'module_type'        => '3',
                'subject'            => 'Invitation to join Challenge',
                'fr_CA_subject'      => 'Invitation à rejoindre le Défi',
                'body_content'       => 'You have been invited to the Challenge component_title by user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the Challenge once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité au Défi component_title par user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté à Défi une fois connecté.',
            ],
            [
                'template_type'      => '0',
                'module_type'        => '5',
                'subject'            => 'Invitation to join Project',
                'fr_CA_subject'      => 'Invitation à rejoindre le projet',
                'body_content'       => 'You have been invited to the Project component_title by user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the Project once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité au projet component_title par user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté au projet une fois connecté.',
            ],
            [
                'template_type'      => '0',
                'module_type'        => '2',
                'subject'            => 'Invitation to join Lab Program',
                'fr_CA_subject'      => 'Invitation à rejoindre le programme Lab',
                'body_content'       => 'You have been invited to the Lab Program component_title by user_name. Use the link below to register and log in on PreprLabs with this email address. You will be added to the Lab Program once you log in.',
                'fr_CA_body_content' => 'Vous avez été invité au programme Lab component_title par user_name. Utilisez le lien ci-dessous pour vous inscrire et vous connecter à PreprLabs avec cette adresse e-mail. Vous serez ajouté au Programme de laboratoire une fois connecté.',
            ],
        ];

        foreach ($emailTemplates as $emailTemplate) {
            EmailTemplate::updateOrCreate([
                'template_type' => $emailTemplate['template_type'],
                'module_type'   => $emailTemplate['module_type'],
            ], [
                'subject'            => $emailTemplate['subject'],
                'fr_CA_subject'      => $emailTemplate['fr_CA_subject'],
                'body_content'       => $emailTemplate['body_content'],
                'fr_CA_body_content' => $emailTemplate['fr_CA_body_content'],
            ]);
        }
    }
}
