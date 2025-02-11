<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminDetailRequest extends FormRequest
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
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i|after:clock_in',
            'breaks.*.start_time' => 'nullable|date_format:H:i|after_or_equal:clock_in|before_or_equal:clock_out',
            'breaks.*.end_time' => 'nullable|date_format:H:i|after:breaks.*.start_time|before_or_equal:clock_out',
            'reason' => 'required',
        ];
    }


    public function messages()
    {
        return [
            'clock_in.required' => '出勤時間を入力してください。',
            'clock_out.required' => '退勤時間を入力してください。',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です。',
            'breaks.*.start_time.after_or_equal' => '休憩時間が勤務時間外です。',
            'breaks.*.start_time.before_or_equal' => '休憩時間が勤務時間外です。',
            'breaks.*.end_time.after' => '休憩終了時間は休憩開始時間より後でなければなりません。',
            'breaks.*.end_time.before_or_equal' => '休憩時間が勤務時間外です。',
            'reason.required' => '備考を記入してください',
        ];
    }
}
