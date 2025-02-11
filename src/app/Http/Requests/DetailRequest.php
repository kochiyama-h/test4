<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetailRequest extends FormRequest
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
            
            'clock_in' => 'before:clock_out',
            'clock_out' => 'after:clock_in',
            
            'start_time' => 'after:clock_in|before:clock_out',
            'end_time' => 'after:clock_in|before:clock_out',
            
            
            'reason' => 'required',
        ];
    }



    public function messages()
    {
        return [
            'clock_in.before' => '出勤時間もしくは退勤時間が不適切な値です。',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'start_time.after' => '休憩時間が勤務時間外です',
            'start_time.before' => '休憩時間が勤務時間外です',
            'end_time.before' => '休憩時間が勤務時間外です',
            'end_time.after' => '休憩時間が勤務時間外です',

            

            
            'reason.required' => '備考を記入してください',
        ];
    }
    
}
