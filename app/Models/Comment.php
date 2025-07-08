<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // BelongsToをインポート

class Comment extends Model
{
    use HasFactory;

    /**
     * モデルの「一括割り当て可能」な属性。
     * （create() や update() メソッドで一度に設定できるカラム）
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',   // コメント投稿者のID
        'answer_id', // コメントが紐付く回答のID
        'content',   // コメントの内容
    ];

    /**
     * このコメントを投稿したユーザーを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このコメントが属する回答を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}