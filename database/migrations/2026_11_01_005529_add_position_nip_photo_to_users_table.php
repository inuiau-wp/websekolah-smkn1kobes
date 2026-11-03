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
        Schema::table('users', function (Blueprint $table) {
            $table->string('position', 100)->nullable()->after('role'); // Kepala Sekolah, Waka Kurikulum, Guru Mapel, dll
            $table->string('nip', 50)->nullable()->after('position');
            $table->string('photo')->nullable()->after('nip');
            $table->text('bio')->nullable()->after('photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'nip', 'photo', 'bio']);
        });
    }
};
