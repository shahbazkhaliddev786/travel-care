<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialMediaLink;
use App\Models\User;

class FooterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user for the updated_by_user_id
        $adminUser = User::where('role', 'admin')->first();
        $adminUserId = $adminUser ? $adminUser->id : null;

        // Create social media links
        $socialMediaLinks = [
            [
                'platform' => 'linkedin',
                'url' => 'https://linkedin.com/company/travelcare',
                'icon_class' => 'fab fa-linkedin-in',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/travelcare',
                'icon_class' => 'fab fa-facebook-f',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'platform' => 'instagram',
                'url' => 'https://instagram.com/travelcare',
                'icon_class' => 'fab fa-instagram',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://youtube.com/c/travelcare',
                'icon_class' => 'fab fa-youtube',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'platform' => 'twitter',
                'url' => 'https://twitter.com/travelcare',
                'icon_class' => 'fab fa-twitter',
                'sort_order' => 5,
                'is_active' => false,
            ],
        ];

        foreach ($socialMediaLinks as $link) {
            SocialMediaLink::updateOrCreate(
                ['platform' => $link['platform']],
                $link
            );
        }

        $this->command->info('Footer content seeded successfully!');
    }
}