<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $procedure = "
            DROP FUNCTION IF EXISTS get_total_berat_hari_ini;
            
            CREATE FUNCTION get_total_berat_hari_ini() 
            RETURNS DECIMAL(10,2)
            DETERMINISTIC
            BEGIN
                DECLARE total_berat DECIMAL(10,2);
                SELECT COALESCE(SUM(berat), 0) INTO total_berat
                FROM transaksi
                WHERE DATE(tgl_masuk) = CURDATE();
                RETURN total_berat;
            END
        ";

        DB::unprepared($procedure);
    }

    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS get_total_berat_hari_ini");
    }
};