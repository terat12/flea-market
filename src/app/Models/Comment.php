<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'product_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 逆参照
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
