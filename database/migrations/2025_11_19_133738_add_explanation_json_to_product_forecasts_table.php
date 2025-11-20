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
        Schema::table('product_forecasts', function (Blueprint $table) {
            // Add a nullable JSON column to store the XAI explanation data.
            // JSON is perfect for structured data like SHAP values or feature importance scores.
            $table->json('explanation_json')->nullable()->after('predicted_sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_forecasts', function (Blueprint $table) {
            // When rolling back, remove the added column.
            $table->dropColumn('explanation_json');
        });
    }
};