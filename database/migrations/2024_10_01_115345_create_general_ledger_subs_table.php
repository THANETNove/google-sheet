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
        Schema::create('general_ledger_subs', function (Blueprint $table) {
            $table->id();
            $table->string('gls_code_company')->nullable();
            $table->string('gls_code')->nullable();
            $table->string('gls_id')->nullable();
            $table->string('gls_gl_code')->nullable();
            $table->string('gls_gl_document')->nullable();
            $table->string('gls_account_code')->nullable();
            $table->string('gls_debit')->nullable();
            $table->string('gls_credit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledger_subs');
    }
};
