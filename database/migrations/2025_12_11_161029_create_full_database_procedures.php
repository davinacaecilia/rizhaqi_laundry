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
                -- KITA PAKSA AGAR PARAMETER STRING SESUAI DENGAN TABEL LARAVEL
                IN p_id_transaksi CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_id_user CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_jumlah DECIMAL(15,2),
                IN p_keterangan VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
            )
            BEGIN
                DECLARE v_total_tagihan DECIMAL(15,2);
                DECLARE v_total_bayar_sekarang DECIMAL(15,2);

                -- 1. Catat Uang Masuk
                INSERT INTO pembayaran (
                    id_pembayaran, id_transaksi, id_user, jlh_pembayaran, 
                    tgl_bayar, keterangan, created_at, updated_at
                ) VALUES (
                    UUID(), p_id_transaksi, p_id_user, p_jumlah, 
                    NOW(), p_keterangan, NOW(), NOW()
                );

                -- 2. Update Jumlah Bayar di Transaksi
                UPDATE transaksi 
                SET jumlah_bayar = jumlah_bayar + p_jumlah
                WHERE id_transaksi = p_id_transaksi;

                -- 3. PANGGIL FUNCTION (Pastikan function ini juga sudah fix collation-nya)
                SET v_total_tagihan = fn_hitung_total_transaksi(p_id_transaksi);

                -- 4. Ambil Total yang SUDAH dibayar
                SELECT jumlah_bayar INTO v_total_bayar_sekarang
                FROM transaksi 
                WHERE id_transaksi = p_id_transaksi;

                -- 5. Cek Status Lunas / DP
                IF v_total_bayar_sekarang >= v_total_tagihan THEN
                    UPDATE transaksi SET status_bayar = 'lunas' WHERE id_transaksi = p_id_transaksi;
                ELSE
                    UPDATE transaksi SET status_bayar = 'dp' WHERE id_transaksi = p_id_transaksi;
                END IF;
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
                IN p_id_transaksi CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_id_user CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
            )
            BEGIN
                DECLARE v_status_bayar VARCHAR(20);
                DECLARE v_kode_invoice VARCHAR(50);
                
                SELECT status_bayar, kode_invoice 
                INTO v_status_bayar, v_kode_invoice
                FROM transaksi 
                WHERE id_transaksi = p_id_transaksi;

                IF v_status_bayar <> 'lunas' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'GAGAL: Transaksi belum lunas! Harap lunasi pembayaran sebelum menyelesaikan order.';
                ELSE
                    UPDATE transaksi 
                    SET status_pesanan = 'selesai',
                        updated_at = NOW()
                    WHERE id_transaksi = p_id_transaksi;

                    INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                    VALUES (
                        UUID(),
                        p_id_user,
                        'SERAH TERIMA',
                        CONCAT('Order ', v_kode_invoice, ' telah diambil & diselesaikan.'),
                        NOW()
                    );
                END IF;
            END
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS sp_update_status_transaksi");
        DB::unprepared("
            CREATE PROCEDURE sp_update_status_transaksi(
                IN p_id_transaksi CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_status_baru VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_id_user CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
            )
            BEGIN
                -- PERBAIKAN DISINI: Variabel internal juga harus dipaksa unicode_ci
                DECLARE v_status_lama VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
                DECLARE v_invoice VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

                -- 1. Ambil data
                SELECT status_pesanan, kode_invoice INTO v_status_lama, v_invoice
                FROM transaksi 
                WHERE id_transaksi = p_id_transaksi;

                -- 2. Validasi (Opsional)
                IF v_status_lama = 'selesai' AND p_status_baru != 'diterima' THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'GAGAL: Transaksi sudah selesai, status terkunci!';
                END IF;

                -- 3. Proses Update
                -- Sekarang aman karena kedua variabel sudah unicode_ci
                IF v_status_lama <> p_status_baru THEN
                    
                    UPDATE transaksi 
                    SET status_pesanan = p_status_baru, 
                        updated_at = NOW()
                    WHERE id_transaksi = p_id_transaksi;

                    INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                    VALUES (
                        UUID(), 
                        p_id_user, 
                        'GANTI STATUS', 
                        CONCAT('Invoice ', v_invoice, ' status berubah: ', v_status_lama, ' -> ', p_status_baru),
                        NOW()
                    );
                    
                END IF;
            END
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_status_counts");
        DB::unprepared("
            CREATE PROCEDURE sp_get_status_counts()
            BEGIN
                SELECT 
                    LOWER(
                        CASE 
                            WHEN status_pesanan = 'siap diambil' THEN 'siap'
                            ELSE status_pesanan 
                        END
                    ) as status_kategori,
                    COUNT(*) as total
                FROM transaksi
                GROUP BY status_kategori;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_input_pembayaran");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ambil_cucian");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_update_status_transaksi");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_status_counts");
    }
};