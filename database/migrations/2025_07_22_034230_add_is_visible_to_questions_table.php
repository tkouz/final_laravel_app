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
                // is_visible カラムが存在しない場合のみ追加
                if (!Schema::hasColumn('questions', 'is_visible')) {
                    $table->boolean('is_visible')->default(true)->after('image_path'); // image_path の後に配置
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('questions', function (Blueprint $table) {
                // is_visible カラムが存在する場合のみ削除
                if (Schema::hasColumn('questions', 'is_visible')) {
                    $table->dropColumn('is_visible');
                }
            });
        }
    };
    