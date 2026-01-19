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
        Schema::create('system_users', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('username');
            $table->string('full_name');
            $table->enum('role', ['analyst', 'inspector', 'broker', 'admin']);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_users');
    }
};
