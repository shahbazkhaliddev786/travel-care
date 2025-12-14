<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterPage;
use App\Models\User;

class FooterPagesSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $adminUserId = $adminUser ? $adminUser->id : null;

        $pages = [
            [
                'slug' => 'terms-conditions',
                'title' => 'Terms & Conditions',
                'content' => '<h2>Terms & Conditions</h2><p>These terms and conditions govern the use of TravelCare services. By accessing or using our platform, you agree to comply with these terms. Please read them carefully.</p>',
                'is_active' => true,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information when you use TravelCare.</p>',
                'is_active' => true,
            ],
            [
                'slug' => 'cookies-policy',
                'title' => 'Cookies Policy',
                'content' => '<h2>Cookies Policy</h2><p>We use cookies to improve your experience on our website. This policy explains what cookies are and how we use them.</p>',
                'is_active' => true,
            ],
            [
                'slug' => 'support',
                'title' => 'Support',
                'content' => '<h2>Support</h2><p>If you need assistance, please reach out to our support team. We are here to help with any issues or questions.</p>',
                'is_active' => true,
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'content' => '<h2>Contact Us</h2><p>Have questions? Contact us via the form or email support@travelcare.com.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            FooterPage::updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'is_active' => $page['is_active'],
                    'updated_by_user_id' => $adminUserId,
                    'last_updated_by' => now(),
                ]
            );
        }
    }
}