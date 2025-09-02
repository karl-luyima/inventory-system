<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->increments('report_id');
            $table->unsignedInteger('analyst_id');
            $table->date('generateDate')->nullable();
            $table->timestamps();

            $table->foreign('analyst_id')->references('analyst_id')->on('sales_analysts')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales_reports');
    }
};
