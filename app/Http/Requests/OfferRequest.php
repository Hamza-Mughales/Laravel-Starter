<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
   * @return array
   */
  public function rules()
  {
    return [
      'name_ar' => 'required|max:100',
      'name_en' => 'required|max:100',
      'price' => 'required|numeric',
      'details_ar' => 'required',
      'details_en' => 'required',
      'photo' => 'required|mimes:png,jpg,jpeg',
    ];
  }

  // Rewrite the messages function from FormRequest
  public function messages()
  {
    return [
      'photo.required' => __('messages.Offer photo required'),
      'name_ar.required' => __('messages.Offer name required'),
      'name_en.required' => __('messages.Offer name required'),
      'name_ar.unique' => __('messages.offer name is  exists'),
      'name_en.unique' => __('messages.offer name is  exists'),
      'price.numeric' => 'سعر العرض يجب ان يكون ارقام',
      'price.required' => 'السعر مطلوب',
      'details_ar.required' => 'ألتفاصيل مطلوبة ',
      'details_en.required' => 'ألتفاصيل مطلوبة ',
      'photo.mimes' =>  'صوره غير صالحة',
    ];
  }
}
