<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_model_id')->constrained();
            $table->float('item_cost', 8, 2);
            $table->string('imei_1');
            $table->string('imei_2')->nullable();
            $table->string('rom_size');
            $table->string('color');
            $table->string('description')->nullable();
            $table->float('item_sellout_price', 8)->nullable();
            $table->boolean('is_new')->default(true);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phones');
    }
}
