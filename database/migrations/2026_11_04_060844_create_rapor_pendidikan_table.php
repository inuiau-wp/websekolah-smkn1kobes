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
        Schema::create('rapor_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique(); // tahun rilis rapor
            $table->string('title')->nullable();            // judul halaman
            $table->text('intro')->nullable();              // kata pengantar & komitmen
            $table->longText('data');                       // JSON: kpi, sections, charts, indicators
            $table->text('disclaimer')->nullable();         // penafian hukum (UU KIP & PDP)
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_pendidikan');
    }
};
