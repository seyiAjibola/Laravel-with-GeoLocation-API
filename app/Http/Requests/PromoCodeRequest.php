<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'promo_code' => ['required', 'unique:promo_codes,code_value'],
            'max_rides' => ['required','integer'],
            'radius' => ['required','numeric', 'between:0,999999.99'],
            'expiry_date' => ['required', 'date_format:Y-m-d'],
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ];
    }

    public function messages()
    {
        return [
            'event_id.exists' => 'Event does not exist!',
        ];
    }
}
