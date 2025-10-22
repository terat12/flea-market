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

    public function getConditionLabelAttribute(): string
    {
        $labels = [1 => '新品', 2 => '未使用に近い', 3 => '目立った傷や汚れなし', 4 => 'やや傷や汚れあり', 5 => '傷や汚れあり', 6 => '全体的に状態が悪い'];
        return $labels[$this->condition] ?? '不明';
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
