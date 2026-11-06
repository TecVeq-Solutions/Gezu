<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Brand;
use App\Models\CreatorProfile;
use App\Models\Campaign;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Create Super Admin User for Filament /admin
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@gezux.io'],
            [
                'name' => 'Sarah Chen (Super Admin)',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 1. Create Default Brand User (brand@gezux.io)
        $brandUser = User::updateOrCreate(
            ['email' => 'brand@gezux.io'],
            [
                'name' => 'Acme AI Studio',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'brand',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $brand = Brand::firstOrCreate(
            ['company_slug' => 'acme-ai-studio'],
            [
                'user_id' => $brandUser->id,
                'company_name' => 'Acme AI Studio',
                'website' => 'https://acme.io',
                'description' => 'Autonomous video generation platform and neural clipping infrastructure.',
                'industry' => 'Technology & AI',
                'country' => 'US',
                'verification_status' => 'verified',
            ]
        );

        // 2. Create Demo Creator User & Profile
        $creatorUser = User::updateOrCreate(
            ['email' => 'alex.rivers@email.com'],
            [
                'name' => 'Alex Rivers',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'clipper',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $creatorProfile = CreatorProfile::firstOrCreate(
            ['username' => 'AlexClips'],
            [
                'user_id' => $creatorUser->id,
                'display_name' => 'Alex Rivers | Tech Cuts',
                'bio' => 'High-retention vertical editor specializing in tech keynotes and podcasts.',
                'country' => 'US',
                'total_verified_views' => 1840000,
                'total_campaigns' => 12,
                'total_earnings' => 1840.00,
                'rating' => 4.95,
            ]
        );

        // 3. Creator Wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $creatorUser->id],
            [
                'currency' => 'USD',
                'available_balance' => 1420.50,
                'pending_balance' => 340.00,
                'total_withdrawn' => 4890.00,
            ]
        );

        // Demo Social Account
        \App\Models\SocialAccount::firstOrCreate(
            ['user_id' => $creatorUser->id, 'platform' => 'TikTok'],
            [
                'username' => 'alexclips_official',
                'display_name' => 'Alex Clips Official',
                'followers_count' => 142000,
                'verified' => true,
                'connected_at' => now()->subDays(30),
            ]
        );

        // 4. Seed Core Campaigns
        $c1 = Campaign::firstOrCreate(
            ['slug' => 'future-labs-podcast-42'],
            [
                'brand_id' => $brand->id,
                'title' => 'The Future of AI — Podcast #42',
                'category' => 'Technology & AI',
                'description' => 'We sat down with leading neural network researchers to discuss autonomous robotics, reasoning models, and agent architectures. Extract 30–60s impactful thought-provoking moments with clean subtitles.',
                'source_video_url' => 'https://youtube.com/watch?v=sample-future-labs-42',
                'source_duration' => '1h 14m',
                'thumbnail' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80',
                'total_budget' => 5000.00,
                'remaining_budget' => 1257.19,
                'reward_rate_per_thousand' => 1.00,
                'currency' => 'USD',
                'target_views' => 5000000,
                'verified_views' => 3742810,
                'status' => 'active',
                'start_at' => now()->subDays(12),
                'end_at' => now()->addDays(18),
            ]
        );

        $c1->countries()->firstOrCreate(
            ['country_code' => 'US'],
            ['country_name' => 'United States', 'reward_multiplier' => 1.00, 'target_views' => 3000000]
        );
        $c1->countries()->firstOrCreate(
            ['country_code' => 'GB'],
            ['country_name' => 'United Kingdom', 'reward_multiplier' => 0.95, 'target_views' => 1000000]
        );

        $c1->platforms()->firstOrCreate(['platform' => 'TikTok']);
        $c1->platforms()->firstOrCreate(['platform' => 'Instagram']);
        $c1->platforms()->firstOrCreate(['platform' => 'YouTube Shorts']);
    }
}
