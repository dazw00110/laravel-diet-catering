<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description');
            $table->string('image_path')->default('products/default.png')->after('description');
            $table->decimal('price', 8, 2);
            $table->integer('calories');
            $table->boolean('is_active')->default(true);
            $table->decimal('promotion_price', 8, 2)->nullable();
            $table->timestamp('promotion_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
