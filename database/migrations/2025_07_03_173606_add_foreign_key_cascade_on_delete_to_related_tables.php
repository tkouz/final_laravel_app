<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // DBファサードをインポート

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 外部キーを削除するヘルパー関数
        $dropForeignKeyIfExists = function (string $table_name, string $column_name, string $referenced_table_name, Blueprint $table) {
            $dbName = DB::connection()->getDatabaseName();
            // ★ここを修正: DB::raw() を削除
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME = '{$table_name}' AND COLUMN_NAME = '{$column_name}' AND REFERENCED_TABLE_NAME = '{$referenced_table_name}'");
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $fk) {
                    // DB::statementを使用して生のSQLで外部キーを削除
                    DB::statement("ALTER TABLE `{$table_name}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                }
            }
        };

        Schema::table('questions', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('questions', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('answers', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('answers', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $dropForeignKeyIfExists('answers', 'question_id', 'questions', $table);
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        Schema::table('comments', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('comments', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $dropForeignKeyIfExists('comments', 'answer_id', 'answers', $table);
            $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
        });

        Schema::table('likes', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('likes', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $dropForeignKeyIfExists('likes', 'question_id', 'questions', $table);
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        Schema::table('bookmarks', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('bookmarks', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $dropForeignKeyIfExists('bookmarks', 'question_id', 'questions', $table);
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ロールバック時も同様に外部キーを削除するヘルパー関数を使用
        $dropForeignKeyIfExists = function (string $table_name, string $column_name, string $referenced_table_name, Blueprint $table) {
            $dbName = DB::connection()->getDatabaseName();
            // ★ここを修正: DB::raw() を削除
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME = '{$table_name}' AND COLUMN_NAME = '{$column_name}' AND REFERENCED_TABLE_NAME = '{$referenced_table_name}'");
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $fk) {
                    DB::statement("ALTER TABLE `{$table_name}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                }
            }
        };

        Schema::table('questions', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('questions', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users'); // cascadeなしで元に戻す
        });

        Schema::table('answers', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('answers', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users');
            $dropForeignKeyIfExists('answers', 'question_id', 'questions', $table);
            $table->foreign('question_id')->references('id')->on('questions');
        });

        Schema::table('comments', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('comments', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users');
            $dropForeignKeyIfExists('comments', 'answer_id', 'answers', $table);
            $table->foreign('answer_id')->references('id')->on('answers');
        });

        Schema::table('likes', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('likes', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users');
            $dropForeignKeyIfExists('likes', 'question_id', 'questions', $table);
            $table->foreign('question_id')->references('id')->on('questions');
        });

        Schema::table('bookmarks', function (Blueprint $table) use ($dropForeignKeyIfExists) {
            $dropForeignKeyIfExists('bookmarks', 'user_id', 'users', $table);
            $table->foreign('user_id')->references('id')->on('users');
            $dropForeignKeyIfExists('bookmarks', 'question_id', 'questions', $table);
            $table->foreign('question_id')->references('id')->on('questions');
        });
    }
};