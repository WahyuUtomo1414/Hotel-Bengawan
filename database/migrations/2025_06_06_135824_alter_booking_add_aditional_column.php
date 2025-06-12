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
            $table->integer('person')->default(0);
            $table->integer('extra_person')->default(0);
            $table->integer('extra_bed')->default(0);

            $table->double('extra_person_price', 8, 2)->default(0.00);
            $table->double('extra_bed_price', 8, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_manages', function (Blueprint $table) {
            $table->dropColumn(['person', 'extra_person','extra_bed', 'extra_person_price', 'extra_bed_price']);
        });
    }
};
