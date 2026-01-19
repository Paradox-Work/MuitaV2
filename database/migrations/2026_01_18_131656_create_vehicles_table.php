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
    Schema::create('vehicles', function (Blueprint $table) {
        $table->string('id')->primary(); 
        $table->string('plate_no')->nullable();
        $table->string('country', 2)->nullable(); 
        $table->string('make')->nullable(); 
        $table->string('model')->nullable(); 
        $table->string('vin')->nullable()->unique(); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
