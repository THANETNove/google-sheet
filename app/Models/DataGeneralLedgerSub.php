<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataGeneralLedgerSub extends Model
{
    use HasFactory;

    protected $table = 'general_ledgers';


    /**
     * Fetch related general_ledger_subs records based on gl_code.
     *
     * @param string $gl_code
     * @return \Illuminate\Support\Collection
     */

    // สร้าง relationship กับ general_ledger_subs
    public function subs()
    {
        return $this->hasMany(GeneralLedgerSub::class, 'gls_gl_code', 'gl_code');
    }
}