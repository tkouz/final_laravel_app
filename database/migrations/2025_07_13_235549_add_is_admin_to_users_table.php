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
        Schema::table('users', function (Blueprint $table) {
            // is_admin カラムを追加。デフォルトはfalse（管理者ではない）
            // nullを許容せず、デフォルト値を設定
            $table->boolean('is_admin')->default(false)->after('email'); // 'email'カラムの後に挿入
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ロールバック時にis_adminカラムを削除
            $table->dropColumn('is_admin');
        });
    }
};