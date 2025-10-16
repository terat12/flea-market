<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('price');
            $table->enum('payment_method', ['convenience', 'card']);
            $table->string('status')->default('completed'); // 簡易：完了で登録
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_building')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};