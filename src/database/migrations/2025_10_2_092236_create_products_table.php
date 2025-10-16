<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // 商品名
            $table->string('brand')->nullable();     // ブランド名
            $table->text('description')->nullable(); // 説明
            $table->unsignedInteger('price');        // 価格
            $table->unsignedTinyInteger('condition')->default(3); // 6段階評価
            $table->string('category')->nullable();  // 簡易カテゴリ
            $table->string('image_path')->nullable(); // 画像（後で）
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
