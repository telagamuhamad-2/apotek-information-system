<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_outgoings', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('product_type_id')->nullable();
            $table->string('product_purpose')->nullable();
            $table->integer('product_quantity')->nullable();
            $table->decimal('product_each_price', 10, 2)->nullable();
            $table->decimal('product_total_price', 10, 2)->nullable();
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_outgoings');
    }
};
