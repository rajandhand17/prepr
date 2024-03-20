<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'title'           => 'No Poverty',
                'fr_CA_title'     => 'Éliminer la pauvreté',
                'tag_image'       => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'fr_CA_tag_image' => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'components'      => 'lab,challenge',
            ], [
                'title'           => 'Zero Hunger',
                'fr_CA_title'     => 'Éliminer la faim dans le monde',
                'tag_image'       => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'fr_CA_tag_image' => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'components'      => 'lab,challenge',
            ], [
                'title'           => 'Good Health and Well-being',
                'fr_CA_title'     => 'Santé et bien-être',
                'tag_image'       => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'fr_CA_tag_image' => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'components'      => 'lab,challenge',
            ], [
                'title'           => 'Quality Education',
                'fr_CA_title'     => 'Une éducation de qualité',
                'tag_image'       => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'fr_CA_tag_image' => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'components'      => 'lab,challenge',
            ], [
                'title'           => 'Gender Equality',
                'fr_CA_title'     => 'Égalité des genres',
                'tag_image'       => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'fr_CA_tag_image' => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'components'      => 'lab,challenge',
            ], [
                'title'           => 'Clean Water and Sanitation',
                'fr_CA_title'     => 'Eau propre et assainissement',
                'tag_image'       => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'fr_CA_tag_image' => 'uploads/tag/Ip2mp95aJOWTQ5yGUUrqHra0tQ5T7Ee80lnZT3I4.jpg',
                'components'      => 'lab,challenge',
            ],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(['title' => $tag['title']], $tag);
        }
    }
}
