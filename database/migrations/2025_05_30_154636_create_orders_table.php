<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_price', 8, 2);
            $table->string('status')->default('unordered'); // unordered = koszyk, in_progress = w realizacji, completed = ukończone, cancelled = przerwane
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('cancelled_at')->nullable()->comment('Data przerwania zamówienia, jeśli anulowane');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
