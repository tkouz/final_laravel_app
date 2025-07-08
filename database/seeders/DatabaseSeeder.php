<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ユーザーを常に作成
        User::factory(10)->create();

        // 質問を20件作成
        Question::factory(20)->create([
            'user_id' => User::all()->random()->id, // 作成済みのユーザーからランダムにIDを割り当てる
        ])->each(function ($question) {
            // 各質問に対して、2〜5個の回答を作成し、紐づける
            $answers = Answer::factory(fake()->numberBetween(2, 5))->create([
                'question_id' => $question->id,
                'user_id' => User::all()->random()->id, // 回答にもユーザーを紐付ける
            ]);

            // ★追加: 質問の約半数（例えば偶数IDの質問）にベストアンサーを設定
            if ($question->id % 2 === 0) { // 例として、質問IDが偶数のものにベストアンサーを設定
                $bestAnswer = $answers->random(); // 質問に紐づく回答の中からランダムに1つ選ぶ
                $question->best_answer_id = $bestAnswer->id;
                $question->is_resolved = true; // 質問を解決済みにする
                $question->save();

                // 回答モデルの is_best_answer フラグも更新 (AnswerControllerのmarkAsBestAnswerで更新されるロジックに合わせる)
                // ただし、シーディングにおいてはQuestionのbest_answer_idが設定されていれば十分な場合が多い
                // 必要であれば、以下を追加
                // $bestAnswer->is_best_answer = true;
                // $bestAnswer->save();
            }
        });
    }
}