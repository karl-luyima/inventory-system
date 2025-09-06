<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->increments('admin_id'); // keep this snake_case
            $table->string('admin_name', 255);
            $table->string('admin_email', 255)->unique();
            $table->string('password'); // required password
            $table->unsignedBigInteger('user_id')->nullable(); // keep if you plan to link with users
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrators');
    }
};
