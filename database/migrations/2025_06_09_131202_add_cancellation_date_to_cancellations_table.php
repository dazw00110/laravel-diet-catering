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
        Schema::table('cancellations', function (Blueprint $table) {
        if (!Schema::hasColumn('cancellations', 'cancellation_date')) {
            $table->date('cancellation_date')->nullable()->after('reason');
        }
    });

    }

    public function down(): void
    {
        Schema::table('cancellations', function (Blueprint $table) {
            $table->dropColumn('cancellation_date');
        });
    }

};
