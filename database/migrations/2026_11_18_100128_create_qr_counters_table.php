<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_counters', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();

            $table->unsignedBigInteger('last_number')
                ->default(0);

            $table->engine = 'InnoDB';
        });

        DB::table('qr_counters')->insert([
            'id' => 1,
            'last_number' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_counters');
    }
};