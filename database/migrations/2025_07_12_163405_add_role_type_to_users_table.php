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
            // 既存のroleカラムを削除
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            // 新しいroleカラムをinteger型で追加し、デフォルト値を設定
            $table->integer('role')->default(0)->after('email')->comment('0: 一般ユーザー, 1: 管理者');
            // もし既存のデータがある場合は、ここでデータの移行ロジックを追加
            // 例: DB::statement("UPDATE users SET role = CASE WHEN old_role_column = 'admin' THEN 1 ELSE 0 END;");
            // ただしmigrate:fresh --seedで毎回データベースを初期化しているなら不要
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // roleカラムを削除
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            // 元のstring型のroleカラムを再追加
            $table->string('role')->default('user')->after('email');
        });
    }
};