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
    Schema::table('deals', function (Blueprint $table) {
        $table->enum('payment_status', [
            'pending',
            'partial_payment',
            'fully_paid',
        ])->default('pending')->after('down_payment');
    });
}

public function down(): void
{
    Schema::table('deals', function (Blueprint $table) {
        $table->dropColumn('payment_status');
    });
}

    /**
     * Reverse the migrations.
     */

};
