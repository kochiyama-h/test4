<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AttendanceRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => 1, // ユーザーIDを指定
            'clock_in' => '2025-02-15 09:00:00',
            'clock_out' => '2025-02-15 17:00:00',
            'date' => '2025-02-15', 
            'status' => 'left',
            'created_at' => now(),
            'updated_at' => now(),
          ];
          DB::table('attendance_records')->insert($param);
    }
}
