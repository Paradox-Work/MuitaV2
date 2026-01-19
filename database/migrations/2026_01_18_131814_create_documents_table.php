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
    Schema::create('documents', function (Blueprint $table) {
        $table->string('id')->primary(); 
        $table->string('case_id')->nullable();
        $table->string('filename');
        $table->string('mime_type')->nullable();
        $table->string('category')->nullable();
        $table->integer('pages')->nullable();
        $table->string('uploaded_by')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
