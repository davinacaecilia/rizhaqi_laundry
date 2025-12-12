<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ==============================================================================
        // 1. FUNCTION: HITUNG TOTAL TRANSAKSI (CORE)
        // ==============================================================================
        // Pengganti kolom 'total_biaya'. Menghitung total live dari detail_transaksi.
        DB::unprepared("DROP FUNCTION IF EXISTS fn_hitung_total_transaksi");
        DB::unprepared("
            CREATE FUNCTION fn_hitung_total_transaksi(p_id_transaksi CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci) 
            RETURNS DECIMAL(15,2)
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                DECLARE v_total DECIMAL(15,2);

                SELECT COALESCE(SUM(jumlah * harga_saat_transaksi), 0) INTO v_total
                FROM detail_transaksi
                WHERE id_transaksi = p_id_transaksi; 
                
                RETURN v_total;
            END
        ");

        // ==============================================================================
        // 2. FUNCTION: HITUNG SISA TAGIHAN
        // ==============================================================================
        // (Total Biaya - Jumlah Bayar). Berguna untuk tabel index.
        DB::unprepared("DROP FUNCTION IF EXISTS fn_sisa_tagihan");
        DB::unprepared("
            CREATE FUNCTION fn_sisa_tagihan(p_id_transaksi CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci) 
            RETURNS DECIMAL(15,2)
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(15,2);
                DECLARE v_bayar DECIMAL(15,2);
                DECLARE v_sisa DECIMAL(15,2);

                -- Hitung Total (Manual query biar mandiri)
                SELECT COALESCE(SUM(jumlah * harga_saat_transaksi), 0) INTO v_total
                FROM detail_transaksi WHERE id_transaksi = p_id_transaksi;

                -- Ambil yang sudah dibayar
                SELECT jumlah_bayar INTO v_bayar 
                FROM transaksi WHERE id_transaksi = p_id_transaksi;

                SET v_sisa = v_total - v_bayar;

                -- Jika kembalian (negatif), anggap 0 (lunas)
                IF v_sisa < 0 THEN SET v_sisa = 0; END IF;

                RETURN v_sisa;
            END
        ");

        // ==============================================================================
        // 5. FUNCTION: TOTAL BERAT HARI INI (OPERASIONAL)
        // ==============================================================================
        // Monitoring kapasitas mesin.
        DB::unprepared("DROP FUNCTION IF EXISTS fn_total_berat_hari_ini");
        DB::unprepared("
            CREATE FUNCTION fn_total_berat_hari_ini() 
            RETURNS DECIMAL(10,2)
            READS SQL DATA
            BEGIN
                DECLARE v_total_berat DECIMAL(10,2);
                
                SELECT COALESCE(SUM(berat), 0) INTO v_total_berat
                FROM transaksi 
                WHERE DATE(tgl_masuk) = CURDATE() AND status_pesanan != 'batal';

                RETURN v_total_berat;
            END
        ");

        // ==============================================================================
        // 6. FUNCTION: PENDAPATAN BERSIH (LAPORAN)
        // ==============================================================================
        // Uang Masuk (Pembayaran) - Uang Keluar (Pengeluaran).
        DB::unprepared("DROP FUNCTION IF EXISTS fn_pendapatan_bersih");
        DB::unprepared("
            CREATE FUNCTION fn_pendapatan_bersih(p_bulan INT, p_tahun INT) 
            RETURNS DECIMAL(15,2)
            READS SQL DATA
            BEGIN
                DECLARE v_pemasukan DECIMAL(15,2);
                DECLARE v_pengeluaran DECIMAL(15,2);

                -- Uang Masuk (Tabel Pembayaran)
                SELECT COALESCE(SUM(jlh_pembayaran), 0) INTO v_pemasukan
                FROM pembayaran 
                WHERE MONTH(tgl_bayar) = p_bulan AND YEAR(tgl_bayar) = p_tahun;

                -- Uang Keluar (Tabel Pengeluaran)
                SELECT COALESCE(SUM(jumlah), 0) INTO v_pengeluaran
                FROM pengeluaran 
                WHERE MONTH(tanggal) = p_bulan AND YEAR(tanggal) = p_tahun;

                RETURN (v_pemasukan - v_pengeluaran);
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP FUNCTION IF EXISTS fn_hitung_total_transaksi");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_sisa_tagihan");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_total_berat_hari_ini");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_pendapatan_bersih");
    }
};