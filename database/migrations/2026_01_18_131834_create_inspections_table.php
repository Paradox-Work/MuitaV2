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
   Schema::create('inspections', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->string('case_id');
    $table->string('type')->nullable();
    $table->string('result')->default('pending');
    $table->json('checks')->nullable();
    $table->text('comments')->nullable();
    $table->string('assigned_to')->nullable(); 
    $table->string('location')->nullable();     
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
