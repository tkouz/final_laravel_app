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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // 報告したユーザーのID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 報告対象のポリモーフィックリレーション (reportable_id と reportable_type)
            // reportable_id は対象のID (例: question_id, answer_id)
            // reportable_type は対象のモデル名 (例: App\Models\Question, App\Models\Answer)
            $table->nullableMorphs('reportable'); // nullを許容することで、reportable_idとtypeがなくても作成可能に

            // 報告理由 (選択式なので文字列で保存)
            $table->string('reason');

            // 報告コメント (任意なのでnullable)
            $table->text('comment')->nullable();

            // 報告のステータス (例: pending, reviewed, rejected)
            $table->string('status')->default('pending'); // デフォルトは「保留中」

            $table->timestamps(); // created_at と updated_at
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