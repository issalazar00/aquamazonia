<?php

use App\RecursoNecesario;
use App\RecursoSiembra;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnToRecursosNecesarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recursos_necesarios', function (Blueprint $table) {

            $table->foreignId('siembra_id')->nullable();
        });

        DB::statement(' UPDATE recursos_necesarios
        INNER JOIN recursos_siembras ON recursos_necesarios.id = recursos_siembras.id_registro
        SET recursos_necesarios.siembra_id = recursos_siembras.id_siembra
        WHERE recursos_siembras.id_registro = recursos_necesarios.id;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recursos_necesarios', function (Blueprint $table) {
            //
        });
    }
}
