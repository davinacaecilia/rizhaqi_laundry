<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('pengeluaran', function (Blueprint $table) {
            // 1. Primary Key (UUID)
            $table->uuid('id_pengeluaran')->primary();
            $table->uuid('id_user'); 
            $table->string('keterangan', 255);
            $table->integer('jumlah');
            $table->date('tanggal');      
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id_user')->on('users')
                ->onDelete('restrict')
                ->name('fk_pengeluaran_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
