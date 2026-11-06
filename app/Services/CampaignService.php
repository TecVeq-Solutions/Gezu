<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    /**
     * Create and initialize a new performance campaign with fund allocation.
     */
    public function createCampaign(Brand $brand, array $data): Campaign
    {
        return DB::transaction(function () use ($brand, $data) {
            $slug = Str::slug($data['title']) . '-' . Str::random(5);
            
            $campaign = Campaign::create([
                'brand_id' => $brand->id,
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? 'Technology & AI',
                'status' => 'draft',
                'content_type' => 'video',
                'source_video_url' => $data['source_video_url'],
                'source_duration' => $data['source_duration'] ?? '00:30:00',
                'thumbnail' => $data['thumbnail'] ?? null,
                'total_budget' => $data['total_budget'],
                'remaining_budget' => $data['total_budget'],
                'reward_rate_per_thousand' => $data['reward_rate_per_thousand'] ?? 1.00,
                'currency' => $data['currency'] ?? 'USD',
                'target_views' => $data['target_views'] ?? 1000000,
                'verified_views' => 0,
                'requirements_json' => $data['requirements'] ?? null,
                'start_at' => now(),
                'end_at' => now()->addDays(30),
            ]);

            // Assign default target countries
            if (!empty($data['countries'])) {
                foreach ($data['countries'] as $country) {
                    $campaign->countries()->create($country);
                }
            } else {
                $campaign->countries()->create([
                    'country_code' => 'US',
                    'country_name' => 'United States',
                    'reward_multiplier' => 1.00,
                    'target_views' => $campaign->target_views,
                ]);
            }

            return $campaign;
        });
    }

    /**
     * Pause an active campaign.
     */
    public function pauseCampaign(Campaign $campaign): bool
    {
        return $campaign->update(['status' => 'paused']);
    }

    /**
     * Resume a paused campaign.
     */
    public function resumeCampaign(Campaign $campaign): bool
    {
        return $campaign->update(['status' => 'active']);
    }
}
