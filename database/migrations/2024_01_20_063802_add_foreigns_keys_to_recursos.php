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
        Schema::table('recursos', function (Blueprint $table) {

            $table->foreignId('brand_id')->nullable();
            $table->foreignId('provider_id')->nullable();
            $table->foreignId('category_id')->nullable();

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');

            $table->foreign('provider_id')
                ->references('id')
                ->on('providers');

            $table->foreign('category_id')
                ->references('id')
                ->on('resource_categories');
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

            $table->dropForeign('recursos_brand_id_foreign');
            $table->dropColumn('brand_id');

            $table->dropForeign('recursos_provider_id_foreign');
            $table->dropColumn('provider_id');

            $table->dropForeign('recursos_category_id_foreign');
            $table->dropColumn('category_id');
        });
    }
};
