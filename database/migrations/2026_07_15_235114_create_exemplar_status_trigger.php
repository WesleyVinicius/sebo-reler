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
        DB::statement("
            CREATE OR REPLACE FUNCTION fn_exemplar_marca_vendido()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.id_venda IS NOT NULL THEN
                    NEW.status := FALSE;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER trg_exemplar_marca_vendido
            BEFORE INSERT OR UPDATE ON exemplar
            FOR EACH ROW
            EXECUTE FUNCTION fn_exemplar_marca_vendido();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_exemplar_marca_vendido ON exemplar');
        DB::statement('DROP FUNCTION IF EXISTS fn_exemplar_marca_vendido');
    }
};
