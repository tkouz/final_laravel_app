<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ★追加: BelongsToManyをインポート
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'image_path',
        'is_resolved',
        'best_answer_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    /**
     * この質問を投稿したユーザーを取得します。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この質問に紐づく回答を取得します。
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * この質問に選ばれたベストアンサーを取得します。
     */
    public function bestAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'best_answer_id');
    }

    /**
     * この質問に紐づく「いいね！」を取得します。
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * 現在のユーザーがこの質問に「いいね！」しているかを確認します。
     */
    public function isLikedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * この質問をブックマークしたユーザーを取得します。
     * ★修正: HasManyからBelongsToManyへ
     */
    public function bookmarkedByUsers(): BelongsToMany // メソッド名を変更して、ユーザー側からのリレーションと区別
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'question_id', 'user_id')->withTimestamps();
    }

    /**
     * 現在のユーザーがこの質問をブックマークしているかを確認します。
     * ★修正: ロジックをBelongsToManyに合わせて変更
     */
    public function isBookmarkedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->bookmarkedByUsers()->where('user_id', $user->id)->exists();
    }
}