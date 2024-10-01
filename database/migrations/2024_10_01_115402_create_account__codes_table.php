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
        Schema::create('account__codes', function (Blueprint $table) {
            $table->id();
            $table->string('acc_code_company')->nullable();
            $table->string('acc_code')->nullable();
            $table->string('acc_name')->nullable();
            $table->string('acc_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account__codes');
    }
};
