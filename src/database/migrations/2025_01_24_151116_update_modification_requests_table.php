<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateModificationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modification_requests', function (Blueprint $table) {
            // タイプごとの修正用カラムを追加
            $table->datetime('before_clock_in')->nullable()->after('after_date'); // 出勤時間（修正前）
            $table->datetime('after_clock_in')->nullable()->after('before_clock_in'); // 出勤時間（修正後）
            $table->datetime('before_clock_out')->nullable()->after('after_clock_in'); // 退勤時間（修正前）
            $table->datetime('after_clock_out')->nullable()->after('before_clock_out'); // 退勤時間（修正後）
            $table->datetime('before_break_start')->nullable()->after('after_clock_out'); // 休憩開始（修正前）
            $table->datetime('after_break_start')->nullable()->after('before_break_start'); // 休憩開始（修正後）
            $table->datetime('before_break_end')->nullable()->after('after_break_start'); // 休憩終了（修正前）
            $table->datetime('after_break_end')->nullable()->after('before_break_end'); // 休憩終了（修正後）

            // 不要なカラムの削除
            $table->dropColumn('type'); // タイプカラム
            $table->dropColumn('before_time'); // 修正前時間カラム
            $table->dropColumn('after_time'); // 修正後時間カラム
            $table->dropForeign(['target_break_id']); // 外部キー制約を削除
            $table->dropColumn('target_break_id'); // 対象休憩IDカラム
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modification_requests', function (Blueprint $table) {
            // 新しいカラムの削除
            $table->dropColumn('before_clock_in');
            $table->dropColumn('after_clock_in');
            $table->dropColumn('before_clock_out');
            $table->dropColumn('after_clock_out');
            $table->dropColumn('before_break_start');
            $table->dropColumn('after_break_start');
            $table->dropColumn('before_break_end');
            $table->dropColumn('after_break_end');

            // 元のカラムを再追加
            $table->enum('type', ['clock_in', 'clock_out', 'break_start', 'break_end'])->after('after_date');
            $table->datetime('before_time')->nullable()->after('type');
            $table->datetime('after_time')->nullable()->after('before_time');
            $table->foreignId('target_break_id')->nullable()->constrained('breaks')->onDelete('cascade');
        });
        
    }
}
