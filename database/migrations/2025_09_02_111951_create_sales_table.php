<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('sales_id');
            $table->unsignedInteger('pdt_id');
            $table->integer('quantity');
            $table->decimal('totalAmount', 10, 2);
            $table->date('date')->nullable();
            $table->timestamps();

            $table->foreign('pdt_id')->references('pdt_id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales');
    }
};
