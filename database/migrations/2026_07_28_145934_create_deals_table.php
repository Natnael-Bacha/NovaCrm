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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('collector_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('deal_amount', 15, 2);
            $table->decimal('down_payment', 15, 2)->default(0);

            $table->enum('payment_cycle', [
                'monthly',
                'quarterly',
                'semi_annually',
                'annually',
            ]);

            $table->unsignedInteger('number_of_installments');

            $table->decimal('installment_amount', 15, 2);

            $table->date('start_date');

            // Commission
            $table->enum('commission_type', [
                'percentage',
                'fixed_amount',
            ]);

            $table->decimal('commission_value', 15, 2);

            $table->enum('beneficiary', [
                'internal_agent',
                'external_agent',
            ]);

            $table->enum('commission_trigger', [
                'immediate',
                'each_payment',
                'full_payment',
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
