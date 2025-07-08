<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// ↓ここから追加する use ステートメント
use Laravel\Sanctum\HasApiTokens; // ★追加：Sanctumを使っていた場合
use Illuminate\Database\Eloquent\Relations\HasMany; // ★追加
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ★追加


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; // ★変更：HasApiTokens を追加

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // ↓ここに追加する $fillable の要素
        'profile_image_path', // ★追加
        'self_introduction',  // ★追加
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    // ↓↓↓ ここから下に、リレーションシップメソッドを追記 ↓↓↓

    /**
     * このユーザーが投稿した質問を取得します。
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * このユーザーが投稿した回答を取得します。
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * このユーザーが投稿したコメントを取得します。
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * このユーザーがブックマークした質問を取得します。
     * ★修正: HasManyからBelongsToManyへ
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'bookmarks', 'user_id', 'question_id')->withTimestamps();
    }

    /**
     * このユーザーが「いいね！」した質問を取得します。
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
