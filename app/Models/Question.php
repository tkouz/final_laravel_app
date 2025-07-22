<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany; // MorphManyをuse

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
        'is_visible', // ★追加: 投稿の表示/非表示フラグ
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_resolved' => 'boolean',
        'is_visible' => 'boolean', // ★追加: boolean型にキャスト
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
     */
    public function bookmarkedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'question_id', 'user_id')->withTimestamps();
    }

    /**
     * 現在のユーザーがこの質問をブックマークしているかを確認します。
     */
    public function isBookmarkedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->bookmarkedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * この質問に対する違反報告を取得します。
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
