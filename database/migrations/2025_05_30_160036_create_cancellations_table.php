<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->text('reason');
            $table->date('cancellation_date')->nullable();
            $table->foreignId('discount_id')->nullable()->constrained('discounts');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellations');
    }
};
