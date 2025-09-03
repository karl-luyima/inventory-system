<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('kpis', function (Blueprint $table) {
        $table->id();
        $table->string('title');   // e.g. "Monthly Sales"
        $table->string('value');   // e.g. "$45,000"
        $table->string('color')->default('blue'); // theme color for card
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpis'); 
    }
};
