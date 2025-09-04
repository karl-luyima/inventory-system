<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_analysts', function (Blueprint $table) {
            $table->increments('analyst_id');
            $table->string('analyst_name', 255);
            $table->string('analyst_email', 255)->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_analysts');
    }
};
