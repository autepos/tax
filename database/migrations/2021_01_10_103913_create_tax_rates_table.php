<?php

use Autepos\Tax\Models\TaxCode;
use Autepos\Tax\Models\TaxRate;
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
        Schema::create((new TaxRate())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('tenant_id')->nullable();
            $table->string('taxable_id')->nullable();
            $table->string('tax_code', 11)->nullable();
            $table->string('country_code')->nullable();
            $table->string('province')->nullable();
            $table->float('percentage')->default(20);
            //$table->boolean('inclusive')->default(false);//TODO: we got this field from Stripe, but do we really need it given that tax behavior is defined on the products table. This specifies if the tax rate is inclusive or exclusive.
            $table->string('tax_type')->default('vat');
            $table->string('status')->default(TaxRate::STATUS_INACTIVE);
            $table->json('meta')->nullable();
            $table->string('description')->nullable();
            $table->string('name')->unique()->nullable();
            $table->timestamps();

            $table->foreign('tax_code')->references('code')->on((new TaxCode())->getTable())->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new TaxRate())->getTable());
    }
};
