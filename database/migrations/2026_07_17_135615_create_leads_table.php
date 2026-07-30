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
        Schema::create('leads', function (Blueprint $table) {
                   $table->id();


            // Customer Information
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone');


            // Lead Details
            $table->string('budget_range')->nullable();

            $table->string('preferred_location')
                ->nullable();


            // Marketing Information
            $table->enum('lead_source', [
                'website',
                'referral',
                'social media',
                'walk_in',
            ]);


            // Type of customer
            $table->enum('lead_type', [
                'buyer',
                'seller',
                'Tenant',
                'investor'
            ]);


            // Sales Pipeline Stage
            $table->enum('current_stage', [
                'new',
                'contacted',
                'qualified',
                'site visit',
                'proposal sent',
                'initial payment',
                'completed',
                'lost'
            ])->default('new');


            // Assigned Sales Agent
            $table->foreignId('agent_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->timestamps();

    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
