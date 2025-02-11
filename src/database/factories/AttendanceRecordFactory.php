<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AttendanceRecord;
use Faker\Generator as Faker;


$factory->define(AttendanceRecord::class, function (Faker $faker) {
    return [
        'user_id' => \App\Models\User::factory(),
        'status' => 'off_duty', // 初期ステータスを勤務外に設定
        'clock_in_time' => now(),
        'clock_out_time' => null,
    ];
});

class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}
