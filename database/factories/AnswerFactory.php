<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\User; // Userモデルをuse
use App\Models\Question; // Questionモデルをuse
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // ユーザーが存在することを確認（回答者）
        $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
        // 質問が存在することを確認（回答対象の質問）
        $questionId = Question::inRandomOrder()->first()->id ?? Question::factory()->create()->id;

        // 日本語の回答内容の候補リスト（質問のダミーデータと同様に意味が通るように）
        $contents = [
            'ご質問ありがとうございます。エラーメッセージから判断すると、おそらくマイグレーションファイルの設定に問題があるか、データベースの再構築がうまくいっていない可能性があります。もう一度マイグレーションファイルを確認してみてください。',
            'Bladeの@foreachループが動作しない場合、まずコントローラからビューにデータが正しく渡されているかdump()やdd()で確認するのが一般的です。変数名が間違っていることもよくあります。',
            'Dockerはコンテナ技術の総称で、SailはLaravelプロジェクトのためにDocker環境を簡単に構築・管理するためのツールです。Sailを使えば、複雑なDockerコマンドを意識せずに開発環境を立ち上げられます。',
            '外部キー制約は、例えば`$table->foreignId(\'user_id\')->constrained()->onDelete(\'cascade\');` のように記述します。`onDelete(\'cascade\')` は、親レコードが削除された場合に子レコードも削除される設定です。',
            'コントローラからビューへのデータ渡しは`return view(\'your.view\', compact(\'data\'));`がシンプルです。複数のデータを渡す場合は`compact(\'data1\', \'data2\')`のように引数を追加するか、配列形式で渡します。',
            'Xdebugを使ったPHPデバッグは非常に強力です。VS CodeのPHP Debug拡張機能と組み合わせ、`php.ini`でXdebugを有効にし、ポート設定を行う必要があります。具体的な設定手順は公式ドキュメントが参考になります。',
            'Gitのコミットメッセージは、`feat: 新機能追加`や`fix: バグ修正`のようにプレフィックスを付け、何をしたのか簡潔に書くのがおすすめです。また、関連する変更はまとめてコミットし、大きすぎる変更は分割すると良いでしょう。',
            'phpMyAdminでは、左側のデータベース名をクリックし、目的のテーブルを選択することで構造やデータを閲覧・編集できます。SQLタブから直接SQLクエリを実行することも可能です。',
            'Eloquentのリレーションシップは、モデル間でデータがどのように関連しているかを定義します。例えば、`User`が`hasMany``Question`、`Question`が`belongsTo``User`といった形です。N+1問題は、`with()`メソッドで解決できます。',
            '500エラーはサーバーサイドで発生したエラーです。まずLaravelのログファイル（`storage/logs/laravel.log`）を確認し、詳細なエラーメッセージを特定します。`APP_DEBUG=true`にしてエラー画面を確認することも有効です。',
        ];

        return [
            'question_id' => $questionId,
            'user_id' => $userId,
            'content' => fake()->randomElement($contents), // 意味の通る日本語の回答
            'is_best_answer' => fake()->boolean(10), // 10%の確率でベストアンサーにする
            'is_visible' => true,
        ];
    }
}