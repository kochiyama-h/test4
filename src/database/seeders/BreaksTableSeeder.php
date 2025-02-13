<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BreakTime; 
use Carbon\Carbon;
use App\Models\AttendanceRecord;

class BreaksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $attendanceRecordId = DB::table('attendance_records')->first()->id;

        $param = [
            'start_time' => '2025-02-15 12:00:00',
            'end_time' => '2025-02-15 13:00:00',
            'attendance_record_id' => $attendanceRecordId,            
            'created_at' => now(),
            'updated_at' => now(),
          ];
          DB::table('breaks')->insert($param);
        
    }
    }

