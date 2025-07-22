<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'profile_image_path',
        'self_introduction',
        'role',
        'is_admin',
        'is_active', // ★追加: 利用停止フラグ
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
        'role' => 'integer',
        'is_admin' => 'boolean', // ★追加: is_adminカラムもbooleanにキャスト
        'is_active' => 'boolean', // ★追加: is_activeカラムもbooleanにキャスト
    ];


    // ↓↓↓ ここから下に、リレーションシップメソッドを追記 ↓↓↓

    /**
     * このユーザーが投稿した質問を取得
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * このユーザーが投稿した回答を取得
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * このユーザーが投稿したコメントを取得
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'bookmarks', 'user_id', 'question_id')->withTimestamps();
    }

    /**
     * このユーザーが「いいね！」した質問を取得
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * このユーザーが行った違反報告を取得します。
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * ユーザーが管理者であるかを確認する
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
