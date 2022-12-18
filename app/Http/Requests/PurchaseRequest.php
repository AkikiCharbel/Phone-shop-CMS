<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'shipping_source' => 'required|string',
            'shipping_date' => 'required|date',
            'phone_list.*.brand_model_id' => 'required',
            'phone_list.*.imei_1' => 'required',
            //            'phone_list.*.imei_2' => 'required',
            'phone_list.*.item_cost' => 'required|numeric',
            'phone_list.*.rom_size' => 'required',
            'phone_list.*.color' => 'required',
            'phone_list.*.description' => 'nullable|string',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
