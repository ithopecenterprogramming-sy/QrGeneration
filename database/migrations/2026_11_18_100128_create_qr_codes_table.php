<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('generation_batch_id')
                ->constrained('generation_batches')
                ->restrictOnDelete();

            $table->unsignedBigInteger('serial_number')
                ->unique();

            $table->string('code', 30)->unique();

            $table->string('qr_content', 100);

            $table->string('image_path')->nullable();

            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};