<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('practice_reports', function (Blueprint $table) {
            // 既存の匿名練習報告は残し、状態変更日時の欄だけを空で追加します。
            $table->timestamp('status_changed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_reports', function (Blueprint $table) {
            $table->dropColumn('status_changed_at');
        });
    }
};
