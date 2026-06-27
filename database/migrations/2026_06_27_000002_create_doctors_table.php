<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('specialization_id')
                ->nullable()
                ->constrained('specializations')
                ->nullOnDelete();

            $table->text('bio')->nullable();
            $table->string('qualification')->nullable();
            $table->unsignedSmallInteger('experience_years')->default(0);

            $table->enum('gender', ['male', 'female'])->nullable();

            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->decimal('consultation_fee', 8, 2)->default(0);

            $table->boolean('is_available')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('city');
            $table->index('is_available');
            $table->index('consultation_fee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
