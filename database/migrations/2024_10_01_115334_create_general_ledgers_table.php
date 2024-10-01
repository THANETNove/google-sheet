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
        Schema::create('general_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('gl_code_company')->nullable();
            $table->string('gl_code')->nullable();
            $table->string('gl_refer')->nullable();
            $table->string('gl_report_vat')->nullable();
            $table->string('gl_date')->nullable();
            $table->string('gl_document')->nullable();
            $table->string('gl_date_check')->nullable();
            $table->string('gl_document_check')->nullable();
            $table->string('gl_company')->nullable();
            $table->string('gl_taxid')->nullable();
            $table->string('gl_branch')->nullable();
            $table->string('gl_code_acc')->nullable();
            $table->string('gl_description')->nullable();
            $table->string('gl_code_acc_pay')->nullable();
            $table->string('gl_date_pay')->nullable();
            $table->string('gl_vat')->nullable();
            $table->string('gl_rate')->nullable();
            $table->string('gl_taxmonth')->nullable();
            $table->string('gl_amount_no_vat')->nullable();
            $table->string('gl_amount')->nullable();
            $table->string('gl_tax')->nullable();
            $table->string('gl_total')->nullable();
            $table->string('gl_url')->nullable();
            $table->string('gl_page')->nullable();
            $table->string('gl_remark')->nullable();
            $table->string('gl_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};
