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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prodi');
            $table->integer('sintaOverall')->default(0);
            $table->integer('sinta3Yr')->default(0);
            $table->integer('scholar')->default(0);
            $table->integer('scopus')->default(0);
            $table->integer('scopusHIndex')->default(0);
            $table->integer('hIndex')->default(0);
            $table->string('sintaId')->nullable();
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
