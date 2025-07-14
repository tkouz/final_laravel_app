<?php
namespace Database\Factories;

    use App\Models\Question;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class QuestionFactory extends Factory
    {
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = Question::class;

        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array
        {
            // ユーザーIDが存在することを確認
            $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;

            // 日本語の質問タイトルと内容の候補リスト
            $titles = [
                'Laravelで質問掲示板を作るには？',
                'Bladeテンプレートの@foreachループが動きません',
                'DockerとSailの違いについて教えてください',
                'マイグレーションファイルで外部キー制約を追加する方法',
                'コントローラからビューにデータを渡すにはどうすれば？',
                'VS CodeでPHPのデバッグ環境を構築したい',
                'Gitのコミット履歴をきれいに保つコツは？',
                'phpMyAdminでデータベースを操作する方法',
                'Eloquentのリレーションシップについて教えてください',
                '開発環境で500エラーが発生した場合のデバッグ方法',
            ];

            $contents = [
                '現在Laravel Sail環境で開発を進めています。基本的な質問掲示板機能の実装について、どのような手順で進めれば良いかアドバイスをいただけますでしょうか。特に認証機能との連携方法が知りたいです。',
                'Bladeファイル内で@foreachを使って配列データを表示しようとしているのですが、何も表示されません。コントローラからはcompactでデータを渡しているつもりです。どこを確認すれば良いでしょうか？',
                'Laravel開発でDockerとSailという言葉をよく聞きますが、それぞれの役割や違いがいまいち理解できていません。初心者にも分かりやすく解説していただけると助かります。',
                '既存のテーブルに新しいテーブルのIDを外部キーとして設定したいのですが、マイグレーションファイルでの正しい記述方法が分かりません。onDeleteなどのオプションについても知りたいです。',
                'コントローラで取得したデータベースのデータを、ビュー（Bladeファイル）に渡して表示する方法について教えてください。特に複数のデータを渡す場合の書き方が知りたいです。',
                'VS CodeでLaravelプロジェクトのPHPコードをステップ実行でデバッグしたいと考えています。Xdebugなどの設定方法から教えていただけますでしょうか。',
                '共同開発でGitを使う際、コミット履歴を後から見て分かりやすいようにするには、どのような点に注意してコミットメッセージを書けば良いでしょうか？rebaseなどの操作も気になります。',
                'phpMyAdminを導入しましたが、データベースのテーブル構造を確認したり、直接データを編集したりする基本的な操作方法が分かりません。よく使う機能について教えてください。',
                'LaravelのEloquent ORMで、UserモデルとQuestionモデルのような異なるモデル間の関連付け（hasMany, belongsToなど）の具体的な使い方と、N+1問題の対策について知りたいです。',
                '開発中に500エラーが発生した際に、原因を特定するための効果的なデバッグ方法を探しています。ログの確認方法や、一時的なデバッグ出力の方法などがあれば教えてください。',
            ];


            return [
                // あらかじめ用意したリストからランダムに選択
                'title' => fake()->randomElement($titles),
                'body' => fake()->randomElement($contents), // 'content' を 'body' に変更
                'user_id' => $userId,
                'image_path' => null,
            ];
        }
    }
    