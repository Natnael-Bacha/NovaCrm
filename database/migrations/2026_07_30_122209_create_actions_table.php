<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();

            
            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();

            
            $table->enum('activity_type', [
                'follow_up_call',
                'meeting',
                'property_visit',
                'email'
            ]);

            
            $table->foreignId('assigned_to')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'done',
                'on_progress'
            ])->default('on_progress');

           
            $table->dateTime('scheduled_time');

           
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
