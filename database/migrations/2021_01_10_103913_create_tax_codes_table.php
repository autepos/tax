<?php

use Autepos\Tax\Models\TaxCode;
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
        Schema::create((new TaxCode())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('tenant_id')->nullable();
            $table->string('code', 11)->unique()->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new TaxCode())->getTable());
    }
};
