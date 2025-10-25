<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'brand',
        'description',
        'price',
        'condition',
        'category',
        'image_path',
        'user_id',
    ];

    public function getCategoryListAttribute(): array
    {
        $val = (string) ($this->category ?? '');
        if ($val === '') return [];

        // 「、」「,」「/」で分割
        $parts = preg_split('/[、,\/]+/u', $val, -1, PREG_SPLIT_NO_EMPTY);

        // 先頭と末尾のあらゆる空白をトリム
        $parts = array_map(
            fn($s) => preg_replace('/^\pZ+|\pZ+$/u', '', $s),
            $parts
        );
        // 空白を除去し、字を詰める
        return array_values(array_filter($parts, fn($s) => $s !== ''));
    }

    //マイリスト関連
    public function likedUsers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'likes')->withTimestamps();
    }

    public function isLikedBy(?\App\Models\User $user): bool
    {
        return $user ? $this->likedUsers()->where('user_id', $user->id)->exists() : false;
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
