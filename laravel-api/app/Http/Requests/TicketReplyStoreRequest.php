<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class TicketReplyStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ticket_code' => 'required|string',
            'message'     => 'required|string',
            'status'      => isThisAdmin() ? 'required|in:0,1,2,3' : 'nullable',
        ];
    }
}
