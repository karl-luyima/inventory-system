<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_forecasts', function (Blueprint $table) {
            $table->id();

            // Match products.pdt_id type (unsigned integer)
            $table->unsignedInteger('pdt_id');  
            
            $table->date('forecast_date');
            $table->float('predicted_sales');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('pdt_id')
                  ->references('pdt_id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_forecasts');
    }
};
