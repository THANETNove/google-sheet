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
        Schema::table('general_ledgers', function (Blueprint $table) {
            $table->index('gl_code_company', 'index_gl_code_company');
            $table->index('gl_date', 'index_gl_date');
        });

        Schema::table('general_ledger_subs', function (Blueprint $table) {
            $table->index('gls_gl_code', 'index_gls_gl_code');
            $table->index('gls_code_company', 'index_gls_code_company');
        });
    }

    public function down()
    {
        Schema::table('general_ledgers', function (Blueprint $table) {
            $table->dropIndex('index_gl_code_company');
            $table->dropIndex('index_gl_date');
        });

        Schema::table('general_ledger_subs', function (Blueprint $table) {
            $table->dropIndex('index_gls_gl_code');
            $table->dropIndex('index_gls_code_company');
        });
    }
};