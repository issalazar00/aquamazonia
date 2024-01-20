<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToRecursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->foreignId('warehouse_id');
            $table->float('cantidad', 20, 2)->default(0);
            $table->float('cantidad_minima', 20, 2)->default(0);
            $table->float('cantidad_maxima', 20, 2)->default(0);

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropForeign('recursos_warehouse_id_foreign');
            $table->dropColumn('warehouse_id');
        });
    }
}
