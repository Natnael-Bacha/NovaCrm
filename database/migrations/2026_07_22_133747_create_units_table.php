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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('unit_number');
            $table->integer('floor');

            $table->enum('unit_type', [
                'apartment',
                'penthouse',
                'office_space',
                'commercial',
                'studio',
                'duplex',
            ]);

            $table->decimal('size', 8, 2);

            $table->decimal('price', 15, 2);

            $table->enum('status', [
                'available',
                'reserved',
                'sold',
            ])->default('available');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
