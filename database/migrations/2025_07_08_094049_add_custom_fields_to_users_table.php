<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // DB::raw() を使う場合に必要

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ↓ここから追加するカラムを記述
            $table->string('profile_image', 255)->nullable()->comment('プロフィール画像')->after('password'); // passwordの後に追加
            $table->text('self_introduction')->nullable()->comment('自己紹介文')->after('profile_image');
            $table->timestamp('last_login_at')->nullable()->comment('最終ログイン日時')->after('self_introduction');
            $table->timestamp('registered_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('登録日時')->after('last_login_at');
            $table->enum('role', ['general', 'admin'])->default('general')->comment('ユーザーロール')->after('registered_at');
            $table->boolean('is_active')->default(true)->comment('有効フラグ')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ↓追加したカラムを削除する記述
            $table->dropColumn([
                'profile_image',
                'self_introduction',
                'last_login_at',
                'registered_at',
                'role',
                'is_active',
            ]);
        });
    }
};