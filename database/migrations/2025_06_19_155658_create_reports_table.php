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
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // 主キー

            // 報告対象タイプ (question or answer)
            $table->enum('reported_object_type', ['question', 'answer'])->comment('報告対象タイプ'); // 必須

            // 報告対象ID (reported_object_typeに応じてquestions.idまたはanswers.id)
            $table->unsignedBigInteger('reported_object_id')->comment('報告対象ID'); // 必須
            // ★ここは外部キー制約を直接貼らない（ポリモーフィックリレーションシップのため）
            //   LaravelのforeignIdFor()などを使うことも検討できるが、enumとの組み合わせでは手動で型を指定

            // 外部キー reporter_user_id (usersテーブルを参照)
            $table->foreignId('reporter_user_id')->constrained('users')->comment('報告ユーザーID'); // 必須

            $table->text('report_reason')->comment('報告理由'); // 必須
            $table->text('report_comment')->nullable()->comment('報告コメント'); // NULL許容

            $table->boolean('is_handled')->default(false)->comment('処理済みフラグ'); // デフォルト FALSE

            // created_at (報告日時として利用) と updated_at (更新日時) を生成
            // テーブル定義書にある 'reported_at' は created_at で代替。
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};