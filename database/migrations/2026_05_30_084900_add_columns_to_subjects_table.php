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
        Schema::table('subjects', function (Blueprint $table) {
            $table->integer('order_number')->nullable()->after('category');
            $table->string('jurusan')->nullable()->after('order_number');
            $table->boolean('tampil_skl')->default(true)->after('jurusan');
            $table->boolean('tampil_transkip')->default(true)->after('tampil_skl');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'jurusan', 'tampil_skl', 'tampil_transkip']);
        });
    }
};
