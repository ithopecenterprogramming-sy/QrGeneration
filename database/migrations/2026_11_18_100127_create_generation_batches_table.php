<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generation_batches', function (Blueprint $table) {
            $table->id();

            $table->string('batch_number')->unique();

            $table->string('prefix', 10)->default('HMC');

            $table->unsignedInteger('quantity');

            $table->unsignedBigInteger('first_number');

            $table->unsignedBigInteger('last_number');

            $table->string('first_serial');

            $table->string('last_serial');

            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generation_batches');
    }
};