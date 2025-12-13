<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. VIEW HARIAN (Perbaikan: Pakai LEFT JOIN)
        DB::unprepared("DROP VIEW IF EXISTS v_laporan_harian");
        DB::unprepared("
            CREATE VIEW v_laporan_harian AS
            SELECT 
                t.id_transaksi,
                t.kode_invoice,
                COALESCE(p.nama, 'Pelanggan Umum') as nama_pelanggan,
                t.tgl_masuk,
                COALESCE(t.berat, 0) as berat,
                fn_hitung_total_transaksi(t.id_transaksi) as total_harga,
                t.status_bayar
            FROM transaksi t
            LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        ");

        // 2. VIEW ARUS KAS (Perbaikan: Konversi ke DATE() biar jam hilang)
        DB::unprepared("DROP VIEW IF EXISTS v_arus_kas");
        DB::unprepared("
            CREATE VIEW v_arus_kas AS
            
            SELECT 
                id_pembayaran as id_ref,
                DATE(tgl_bayar) as tanggal, 
                keterangan,
                jlh_pembayaran as masuk,
                0 as keluar,
                'pemasukan' as jenis
            FROM pembayaran
            
            UNION ALL
            
            -- PENGELUARAN
            SELECT 
                id_pengeluaran as id_ref,
                DATE(tanggal) as tanggal, 
                keterangan,
                0 as masuk,
                jumlah as keluar,
                'pengeluaran' as jenis
            FROM pengeluaran
        ");

        // 3. VIEW REKAP (Otomatis ikut benar karena v_arus_kas sudah diperbaiki)
        DB::unprepared("DROP VIEW IF EXISTS v_rekap_keuangan");
        DB::unprepared("
            CREATE VIEW v_rekap_keuangan AS
            SELECT 
                tanggal,
                SUM(masuk) as total_masuk,
                SUM(keluar) as total_keluar,
                (SUM(masuk) - SUM(keluar)) as bersih
            FROM v_arus_kas
            GROUP BY tanggal
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_rekap_keuangan");
        DB::unprepared("DROP VIEW IF EXISTS v_arus_kas");
        DB::unprepared("DROP VIEW IF EXISTS v_laporan_harian");
    }
};