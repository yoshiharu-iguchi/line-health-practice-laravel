<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeReport extends Model
{
    /**
     * 匿名練習用として、まとめて扱ってよい項目です。
     * 実名、学籍番号、LINEの情報は含めません。
     *
     * @var list<string>
     */
    protected $fillable = [
        'condition',
        'attendance',
        'contact_request',
        'status',
        'status_changed_at',
    ];

    /**
     * SQLiteから取り出した状態変更日時を、日時として扱います。
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_changed_at' => 'datetime',
    ];
}
