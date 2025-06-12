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
        Schema::table('booking_manages', function (Blueprint $table) {
            $table->uuid('payment_id')->nullable();
            $table->string('payment_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_manages', function (Blueprint $table) {
            $table->dropColumn(['payment_url', 'payment_id']);
        });
    }
};
