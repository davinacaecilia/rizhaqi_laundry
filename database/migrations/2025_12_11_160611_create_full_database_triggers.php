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
        // 1. TABEL TRANSAKSI (BEFORE INSERT)
        // Gabungan: Auto Invoice (A-Z, 1-6000) & Default Tanggal Selesai (H+3)
        // ==============================================================================
        DB::unprepared("DROP TRIGGER IF EXISTS trg_bi_transaksi");
        DB::unprepared("
            CREATE TRIGGER trg_bi_transaksi
            BEFORE INSERT ON transaksi
            FOR EACH ROW
            BEGIN
                DECLARE v_last_code VARCHAR(10);
                DECLARE v_last_char CHAR(1);
                DECLARE v_last_num INT;
                DECLARE v_new_char CHAR(1);
                DECLARE v_new_num INT;

                -- A. LOGIKA AUTO INVOICE (A0001 - Z6000)
                -- --------------------------------------
                -- Cek apakah kode_invoice dikirim 'AUTO' atau kosong dari Controller
                IF NEW.kode_invoice = 'AUTO' OR NEW.kode_invoice IS NULL OR NEW.kode_invoice = '' THEN
                    
                    -- Ambil invoice terakhir (Format A0001)
                    SELECT kode_invoice INTO v_last_code 
                    FROM transaksi 
                    WHERE kode_invoice REGEXP '^[A-Z][0-9]{4}$' 
                    ORDER BY created_at DESC, kode_invoice DESC 
                    LIMIT 1;

                    IF v_last_code IS NULL THEN
                        -- Transaksi Pertama
                        SET NEW.kode_invoice = 'A0001';
                    ELSE
                        -- Parsing Huruf & Angka
                        SET v_last_char = LEFT(v_last_code, 1);
                        SET v_last_num = CAST(SUBSTRING(v_last_code, 2) AS UNSIGNED);

                        -- Cek Batas 6000
                        IF v_last_num >= 6000 THEN
                            -- Ganti Huruf (A -> B), Reset Angka
                            SET v_new_char = CHAR(ASCII(v_last_char) + 1);
                            SET v_new_num = 1;
                        ELSE
                            -- Huruf Tetap, Angka Naik
                            SET v_new_char = v_last_char;
                            SET v_new_num = v_last_num + 1;
                        END IF;

                        SET NEW.kode_invoice = CONCAT(v_new_char, LPAD(v_new_num, 4, '0'));
                    END IF;
                END IF;

                -- B. LOGIKA DEFAULT TANGGAL SELESAI
                -- ---------------------------------
                IF NEW.tgl_selesai IS NULL THEN
                    SET NEW.tgl_selesai = DATE_ADD(NEW.tgl_masuk, INTERVAL 3 DAY);
                END IF;
            END
        ");

        // ==============================================================================
        // 2. TABEL TRANSAKSI (AFTER INSERT)
        // Log: Mencatat transaksi baru dibuat
        // ==============================================================================
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_transaksi");
        DB::unprepared("
            CREATE TRIGGER trg_ai_transaksi
            AFTER INSERT ON transaksi
            FOR EACH ROW
            BEGIN
                INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                VALUES (UUID(), NEW.id_user, 'CREATE ORDER', 
                CONCAT('Invoice ', NEW.kode_invoice, ' dibuat. Total Estimasi: Rp ', NEW.total_biaya), NOW());
            END
        ");

        // ==============================================================================
        // 3. TABEL TRANSAKSI (AFTER UPDATE)
        // Log: Mencatat perubahan status cucian & status bayar
        // ==============================================================================
        DB::unprepared("DROP TRIGGER IF EXISTS trg_au_transaksi");
        DB::unprepared("
            CREATE TRIGGER trg_au_transaksi
            AFTER UPDATE ON transaksi
            FOR EACH ROW
            BEGIN
                -- Cek Status Progress
                IF OLD.status_pesanan <> NEW.status_pesanan THEN
                    INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                    VALUES (UUID(), NEW.id_user, 'UPDATE STATUS', 
                    CONCAT('Invoice ', NEW.kode_invoice, ': ', OLD.status_pesanan, ' -> ', NEW.status_pesanan), NOW());
                END IF;

                -- Cek Status Bayar
                IF OLD.status_bayar <> NEW.status_bayar THEN
                    INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                    VALUES (UUID(), NEW.id_user, 'UPDATE BAYAR', 
                    CONCAT('Invoice ', NEW.kode_invoice, ': ', OLD.status_bayar, ' -> ', NEW.status_bayar), NOW());
                END IF;
            END
        ");

        // ==============================================================================
        // 4. TABEL DETAIL TRANSAKSI (BEFORE INSERT)
        // Validasi: Mencegah manipulasi harga di luar rentang (Security)
        // ==============================================================================
        DB::unprepared("DROP TRIGGER IF EXISTS trg_bi_detail_transaksi");
        DB::unprepared("
            CREATE TRIGGER trg_bi_detail_transaksi
            BEFORE INSERT ON detail_transaksi
            FOR EACH ROW
            BEGIN
                DECLARE v_min DECIMAL(10,2);
                DECLARE v_max DECIMAL(10,2);
                
                SELECT harga_satuan, harga_maksimum INTO v_min, v_max 
                FROM layanan WHERE id_layanan = NEW.id_layanan;
                
                IF v_max IS NOT NULL THEN
                    -- Cek Range
                    IF NEW.harga_saat_transaksi < v_min OR NEW.harga_saat_transaksi > v_max THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Security Alert: Harga di luar rentang yang diizinkan!';
                    END IF;
                ELSE
                    -- Cek Harga Tetap (Toleransi 0 rupiah)
                    IF NEW.harga_saat_transaksi <> v_min THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Security Alert: Harga layanan tetap tidak boleh diubah!';
                    END IF;
                END IF;
            END
        ");

        // ==============================================================================
        // 5. TABEL DETAIL TRANSAKSI (AFTER INSERT)
        // Hitung Ulang: Update Total Biaya di Header Transaksi
        // ==============================================================================
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_detail_transaksi");
        DB::unprepared("
            CREATE TRIGGER trg_ai_detail_transaksi
            AFTER INSERT ON detail_transaksi
            FOR EACH ROW
            BEGIN
                DECLARE v_total DECIMAL(15,2);
                
                SELECT SUM(jumlah * harga_saat_transaksi) INTO v_total
                FROM detail_transaksi WHERE id_transaksi = NEW.id_transaksi;
                
                UPDATE transaksi SET total_biaya = v_total WHERE id_transaksi = NEW.id_transaksi;
            END
        ");

        // ==============================================================================
        // 6. LOGGING DATA MASTER & KEUANGAN LAINNYA
        // ==============================================================================
        
        // Log Pembayaran
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_pembayaran");
        DB::unprepared("
            CREATE TRIGGER trg_ai_pembayaran AFTER INSERT ON pembayaran FOR EACH ROW
            BEGIN
                INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                VALUES (UUID(), NEW.id_user, 'TERIMA UANG', CONCAT('Terima Rp ', NEW.jlh_pembayaran, ' (', NEW.keterangan, ')'), NOW());
            END
        ");

        // Log Pengeluaran
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_pengeluaran");
        DB::unprepared("
            CREATE TRIGGER trg_ai_pengeluaran AFTER INSERT ON pengeluaran FOR EACH ROW
            BEGIN
                INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                VALUES (UUID(), NEW.id_user, 'PENGELUARAN', CONCAT('Keluar Rp ', NEW.jumlah, ' untuk ', NEW.keterangan), NOW());
            END
        ");

        // Log Pelanggan Baru
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_pelanggan");
        DB::unprepared("
            CREATE TRIGGER trg_ai_pelanggan AFTER INSERT ON pelanggan FOR EACH ROW
            BEGIN
                INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                VALUES (UUID(), NULL, 'PELANGGAN BARU', CONCAT('Registrasi: ', NEW.nama), NOW());
            END
        ");

        // Log Perubahan Alat
        DB::unprepared("DROP TRIGGER IF EXISTS trg_au_alat");
        DB::unprepared("
            CREATE TRIGGER trg_au_alat AFTER UPDATE ON alat FOR EACH ROW
            BEGIN
                IF OLD.jumlah <> NEW.jumlah THEN
                    INSERT INTO log (id_log, id_user, aksi, keterangan, waktu)
                    VALUES (UUID(), NULL, 'STOK ALAT', CONCAT(NEW.nama_alat, ': ', OLD.jumlah, ' -> ', NEW.jumlah), NOW());
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_bi_transaksi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_transaksi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_au_transaksi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_bi_detail_transaksi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_detail_transaksi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_pembayaran");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_pengeluaran");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ai_pelanggan");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_au_alat");
    }
};