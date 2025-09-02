<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('pdt_id');
            $table->string('pdt_name', 100);
            $table->decimal('price', 10, 2);
            $table->integer('stock_level');
            $table->unsignedInteger('inventory_id');
            $table->timestamps();

            $table->foreign('inventory_id')->references('inventory_id')->on('inventories')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
