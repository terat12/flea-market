<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id'); // ← 追加
        });
    }
    public function down(): void
    {
        Schema::table('products', function (Blueprint $t) {
            $t->dropConstrainedForeignId('user_id'); // ← 追加した約束を外す
        });
    }
};