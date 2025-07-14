<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// DB::raw() を使う場合に備えて追加（もしCURRENT_TIMESTAMPを使うなら）
// use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id(); // 主キー

            // 外部キー question_id (questionsテーブルを参照)
            $table->unsignedBigInteger('question_id')->comment('質問ID'); // 必須

            $table->text('content')->comment('回答本文'); // 必須
            $table->string('image_path', 255)->nullable()->comment('画像パス'); // NULL許容

            // 外部キー user_id (usersテーブルを参照)
            $table->foreignId('user_id')->constrained()->comment('ユーザーID'); // 必須

            // ★ここに追加：ベストアンサーフラグ
            $table->boolean('is_best_answer')->default(false)->comment('ベストアンサーフラグ'); 

            $table->boolean('is_visible')->default(true)->comment('表示フラグ'); // デフォルト TRUE

            // created_at (投稿日時として利用) と updated_at (更新日時) を生成
            // テーブル定義書にある 'posted_at' は created_at で代替。
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};