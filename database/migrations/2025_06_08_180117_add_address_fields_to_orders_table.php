<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('street')->nullable();
            $table->string('apartment_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['city', 'postal_code', 'street', 'apartment_number']);
        });
    }

};
