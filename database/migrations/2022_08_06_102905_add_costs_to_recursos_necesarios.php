<?php

use App\Alimento;
use App\RecursoNecesario;
use App\Recursos;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AddCostsToRecursosNecesarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recursos_necesarios', function (Blueprint $table) {
            $table->float('costo_minutos_hombre', 20, 2)->nullable();
            $table->float('costo_alimento', 20, 2)->nullable();
            $table->float('costo_recurso', 20, 2)->nullable();
        });

        $minutos_hombre = Recursos::select()->where('recurso', 'Minutos hombre')->orWhere('recurso', 'Minuto hombre')->orWhere('recurso', 'Minutos')->first();

        $recursosNecesarios = RecursoNecesario::get();

        foreach ($recursosNecesarios as $recursoNecesario) {
            $alimento = Alimento::select('id','costo_kg')->find($recursoNecesario->id_alimento);
            $recurso = Recursos::select('id','costo')->find($recursoNecesario->id_recurso);

            $registro = RecursoNecesario::find($recursoNecesario->id);
            $registro->costo_recurso = $recurso ?  $recurso->costo : 0;
            $registro->costo_alimento = $alimento ?  $alimento->costo_kg : 0;
            $registro->costo_minutos_hombre = $minutos_hombre->costo;
            $registro->save();
        }
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
