<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==============================================================================
        // 1. PROCEDURE: INPUT PEMBAYARAN (Otomatis Cek Lunas)
        // ==============================================================================
        // Manfaat: Controller tinggal kirim ID & Jumlah Uang.
        // Procedure ini yang akan:
        // a. Insert ke tabel pembayaran
        // b. Update total bayar di tabel transaksi
        // c. Otomatis ubah status jadi 'Lunas' jika uang pas/lebih, atau 'DP' jika kurang.
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_input_pembayaran");
        DB::unprepared("
            CREATE PROCEDURE sp_input_pembayaran(
                IN p_id_transaksi CHAR(36),
                IN p_id_user CHAR(36),
                IN p_jumlah DECIMAL(15,2),
                IN p_keterangan VARCHAR(255)
            )
            BEGIN
                DECLARE v_total_biaya DECIMAL(15,2);
                DECLARE v_sudah_bayar DECIMAL(15,2);
                DECLARE v_total_masuk DECIMAL(15,2);

                -- 1. Insert ke tabel pembayaran
                INSERT INTO pembayaran (id_pembayaran, id_transaksi, id_user, jlh_pembayaran, tgl_bayar, keterangan, created_at, updated_at)
                VALUES (UUID(), p_id_transaksi, p_id_user, p_jumlah, NOW(), p_keterangan, NOW(), NOW());

                -- 2. Ambil data keuangan terkini dari transaksi
                SELECT total_biaya, jumlah_bayar INTO v_total_biaya, v_sudah_bayar
                FROM transaksi WHERE id_transaksi = p_id_transaksi;

                -- 3. Hitung total uang yang masuk (yg lama + yg baru diinput)
                SET v_total_masuk = v_sudah_bayar + p_jumlah;

                -- 4. Update Header Transaksi
                UPDATE transaksi 
                SET jumlah_bayar = v_total_masuk,
                    status_bayar = CASE 
                        WHEN v_total_masuk >= v_total_biaya THEN 'lunas'
                        ELSE 'dp'
                    END
                WHERE id_transaksi = p_id_transaksi;
            END
        ");

        // ==============================================================================
        // 2. PROCEDURE: SERAH TERIMA BARANG (Validasi Lunas)
        // ==============================================================================
        // Manfaat: Mencegah pegawai menyerahkan cucian kalau belum lunas.
        // Procedure ini akan ERROR jika status belum lunas.
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ambil_cucian");
        DB::unprepared("
            CREATE PROCEDURE sp_ambil_cucian(
                IN p_id_transaksi CHAR(36),
                IN p_id_user CHAR(36)
            )
            BEGIN
                DECLARE v_status_bayar VARCHAR(20);
                
                SELECT status_bayar INTO v_status_bayar 
                FROM transaksi WHERE id_transaksi = p_id_transaksi;

                -- Validasi: Harus Lunas dulu
                IF v_status_bayar <> 'lunas' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'GAGAL: Cucian belum lunas, tidak bisa diambil!';
                ELSE
                    -- Jika aman, update status jadi 'diambil'
                    UPDATE transaksi 
                    SET status_pesanan = 'diambil', updated_at = NOW()
                    WHERE id_transaksi = p_id_transaksi;
                    
                    -- Trigger Log akan otomatis mencatat ini
                END IF;
            END
        ");

        // ==============================================================================
        // 3. PROCEDURE: PEMBATALAN TRANSAKSI (Rollback Stok/Log)
        // ==============================================================================
        // Manfaat: Mencatat alasan pembatalan dengan rapi.
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_batalkan_transaksi");
        DB::unprepared("
            CREATE PROCEDURE sp_batalkan_transaksi(
                IN p_id_transaksi CHAR(36),
                IN p_id_user CHAR(36),
                IN p_alasan TEXT
            )
            BEGIN
                -- Update status jadi 'batal'
                UPDATE transaksi 
                SET status_pesanan = 'batal', 
                    catatan = CONCAT(catatan, ' [DIBATALKAN: ', p_alasan, ']'),
                    updated_at = NOW()
                WHERE id_transaksi = p_id_transaksi;

                -- Insert Log Manual (Karena trigger update biasa pesannya standar)
                INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                VALUES (UUID(), p_id_user, 'CANCEL ORDER', CONCAT('Transaksi dibatalkan. Alasan: ', p_alasan), NOW());
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_input_pembayaran");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ambil_cucian");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_batalkan_transaksi");
    }
};