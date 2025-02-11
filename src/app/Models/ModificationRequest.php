<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_record_id',
        'before_date',
        'after_date',
        'before_clock_in',
        'after_clock_in',
        'before_clock_out',
        'after_clock_out',
        'before_break_start',
        'after_break_start',
        'before_break_end',
        'after_break_end',
        'reason',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function breakTime()
    {
        return $this->belongsTo(BreakTime::class, 'target_break_id');
    }


}
