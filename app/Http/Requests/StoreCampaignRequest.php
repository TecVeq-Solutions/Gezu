<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'source_video_url' => ['required', 'url', 'max:500'],
            'source_duration' => ['nullable', 'string', 'max:50'],
            'total_budget' => ['required', 'numeric', 'min:100'],
            'reward_rate_per_thousand' => ['required', 'numeric', 'min:0.10', 'max:50.00'],
            'target_views' => ['required', 'integer', 'min:1000'],
            'currency' => ['nullable', 'string', 'size:3'],
            'requirements' => ['nullable', 'array'],
            'countries' => ['nullable', 'array'],
        ];
    }
}
