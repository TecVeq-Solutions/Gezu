<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitClipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'platform' => ['required', 'string', 'in:TikTok,Instagram,YouTube Shorts,Facebook,X'],
            'post_url' => ['required', 'url', 'max:500'],
            'external_post_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}
