<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_clerks', function (Blueprint $table) {
            $table->increments('clerk_id');
            $table->string('clerk_name', 255);
            $table->string('clerk_email', 255)->unique();
            $table->string('password'); // required password
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_clerks');
    }
};