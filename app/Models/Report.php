<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ★ 後でこのEnumクラスを作成
use App\Enums\ReportedObjectType; 

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reported_object_type',
        'reported_object_id',
        'reporter_user_id',
        'report_reason',
        'report_comment',
        'is_handled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reported_object_type' => ReportedObjectType::class, // ★ Enumキャスト
        'is_handled' => 'boolean',
    ];

    /**
     * Get the user who reported the object.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id'); // 外部キー名がデフォルトと異なるため指定
    }

    /**
     * Get the parent reported model (question or answer).
     */
    public function reported_object()
    {
        return $this->morphTo();
    }
}