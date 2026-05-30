<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('class');
            $table->string('password')->nullable()->after('status');
            $table->string('tahun_lulus', 4)->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['jurusan', 'password', 'tahun_lulus']);
        });
    }
};
