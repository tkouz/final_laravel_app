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
            Schema::table('questions', function (Blueprint $table) {
                // statusカラムを追加。デフォルトは'open'で、NULLを許容しない
                // default('open') を追加することで、既存の行にも'open'が設定される
                $table->string('status')->default('open');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
};
    