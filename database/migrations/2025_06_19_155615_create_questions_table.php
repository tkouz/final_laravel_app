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
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('body');
                $table->unsignedBigInteger('best_answer_id')->nullable(); // 外部キー制約は別のマイグレーションで追加済み
                $table->boolean('is_resolved')->default(false);
                $table->string('image_path', 255)->nullable(); // ★この行を追加！画像パス
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('questions');
        }
    };
    