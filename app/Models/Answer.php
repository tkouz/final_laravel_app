<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // BelongsToをuse
use Illuminate\Database\Eloquent\Relations\HasMany; // HasManyをuse
use Illuminate\Database\Eloquent\Relations\MorphMany; // MorphManyをuse

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'content',
        'image_path',
        'is_best_answer',
        'is_visible', // ★追加: 投稿の表示/非表示フラグ
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_best_answer' => 'boolean',
        'is_visible' => 'boolean', // ★追加: boolean型にキャスト
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * この回答に対する違反報告を取得します。
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
