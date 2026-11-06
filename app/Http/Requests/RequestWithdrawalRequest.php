<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:20.00'],
            'method' => ['required', 'string', 'in:Wise,Bank Transfer,Crypto (USDC),PayPal'],
            'destination_reference' => ['required', 'string', 'max:255'],
        ];
    }
}
