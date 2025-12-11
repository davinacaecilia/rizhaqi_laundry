<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL USERS (PK: UUID)
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id_user')->primary(); // <--- GANTI JADI UUID
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'owner', 'pegawai'])->default('pegawai');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. TABEL PELANGGAN (PK: UUID)
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->uuid('id_pelanggan')->primary(); // <--- GANTI JADI UUID
            $table->string('nama', 100);
            $table->string('telepon', 15);
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        // 3. TABEL LAYANAN (PK: UUID)
        Schema::create('layanan', function (Blueprint $table) {
            $table->uuid('id_layanan')->primary(); // <--- GANTI JADI UUID
            $table->string('kategori', 100);
            $table->string('nama_layanan', 100);
            $table->enum('satuan', ['kg', 'pcs', 'm2']);
            $table->integer('harga_satuan');
            $table->integer('harga_maksimum')->nullable();
            $table->timestamps();
        });

        // 4. TABEL ALAT (PK: UUID)
        Schema::create('alat', function (Blueprint $table) {
            $table->uuid('id_alat')->primary(); // <--- GANTI JADI UUID
            $table->string('nama_alat', 50);
            $table->integer('jumlah');
            $table->date('tgl_maintenance_terakhir')->nullable();
            $table->timestamps();
        });

        // 5. TABEL TRANSAKSI (PK: UUID)
        Schema::create('transaksi', function (Blueprint $table) {
            $table->uuid('id_transaksi')->primary(); // <--- GANTI JADI UUID
            $table->string('kode_invoice', 50)->unique(); // Tetap String pendek (TRX-001) biar enak dibaca manusia
            
            // Relasi UUID
            $table->foreignUuid('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->onDelete('cascade');
            $table->foreignUuid('id_user')->constrained('users', 'id_user');

            $table->dateTime('tgl_masuk')->useCurrent();
            $table->date('tgl_selesai')->nullable();
            $table->float('berat')->default(0); 
            $table->integer('total_biaya');
            $table->integer('jumlah_bayar')->default(0);
            $table->enum('status_bayar', ['belum', 'dp', 'lunas'])->default('belum');
            $table->enum('status_pesanan', ['diterima', 'dicuci', 'dikeringkan', 'disetrika', 'siap diambil', 'selesai'])->default('diterima');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 6. TABEL DETAIL TRANSAKSI (PK: UUID)
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->uuid('id_detail')->primary(); // <--- GANTI JADI UUID
            
            // Relasi UUID
            $table->foreignUuid('id_transaksi')->constrained('transaksi', 'id_transaksi')->onDelete('cascade');
            $table->foreignUuid('id_layanan')->constrained('layanan', 'id_layanan');

            $table->float('jumlah'); 
            $table->integer('harga_saat_transaksi'); 
            $table->timestamps();
        });

        // 7. TABEL TRANSAKSI INVENTARIS (PK: UUID)
        Schema::create('transaksi_inventaris', function (Blueprint $table) {
            $table->uuid('id_inventaris')->primary(); // <--- GANTI JADI UUID
            $table->foreignUuid('id_transaksi')->constrained('transaksi', 'id_transaksi')->onDelete('cascade');
            $table->string('nama_barang', 100);
            $table->integer('jumlah');
            $table->timestamps();
        });

        // 8. TABEL PEMBAYARAN (PK: UUID)
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->uuid('id_pembayaran')->primary(); // <--- GANTI JADI UUID
            
            $table->foreignUuid('id_transaksi')->constrained('transaksi', 'id_transaksi')->onDelete('cascade');
            $table->foreignUuid('id_user')->constrained('users', 'id_user');

            $table->integer('jlh_pembayaran');
            $table->dateTime('tgl_bayar')->useCurrent();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });


        // 10. TABEL LAPORAN HARIAN PEGAWAI (PK: UUID)
        Schema::create('laporan_harian_pegawai', function (Blueprint $table) {
            $table->uuid('id_laporan')->primary(); // <--- GANTI JADI UUID
            $table->foreignUuid('id_user')->constrained('users', 'id_user');
            $table->foreignUuid('id_transaksi')->constrained('transaksi', 'id_transaksi');
            $table->date('tgl_dikerjakan');
            $table->timestamps();
        });

        // 11. TABEL LOG (PK: UUID)
        Schema::create('log', function (Blueprint $table) {
            $table->uuid('id_log')->primary(); // <--- GANTI JADI UUID
            $table->uuid('id_user')->nullable();
            $table->string('aksi', 50);
            $table->text('keterangan')->nullable();
            $table->timestamp('waktu')->useCurrent();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log');
        Schema::dropIfExists('laporan_harian_pegawai');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('transaksi_inventaris');
        Schema::dropIfExists('detail_transaksi');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('alat');
        Schema::dropIfExists('layanan');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('users');
    }
};