<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable();
            $table->foreignId('provider_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('warehouse_id')->nullable();

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');

            $table->foreign('provider_id')
                ->references('id')
                ->on('providers');

            $table->foreign('category_id')
                ->references('id')
                ->on('resource_categories');

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
        Schema::table('alimentos', function (Blueprint $table) {
            $table->dropForeign('alimentos_brand_id_foreign');
            $table->dropColumn('brand_id');

            $table->dropForeign('alimentos_provider_id_foreign');
            $table->dropColumn('provider_id');

            $table->dropForeign('alimentos_category_id_foreign');
            $table->dropColumn('category_id');

            $table->dropForeign('alimentos_warehouse_id_foreign');
            $table->dropColumn('warehouse_id');
        });
    }
};
