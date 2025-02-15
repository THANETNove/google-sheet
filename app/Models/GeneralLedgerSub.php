<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedgerSub extends Model
{
    use HasFactory;
    protected $table = 'general_ledger_subs';
    protected $primaryKey = 'gls_gl_code';
    public $incrementing = false;
    protected $keyType = 'string';



    protected $fillable = [
        'gls_code_company',
        'gls_code',
        'gls_id',
        'gls_gl_code',
        'gls_gl_document',
        'gls_gl_date',
        'gls_account_code',
        'gls_account_name',
        'gls_debit',
        'gls_credit',
    ];

    public function ledger()
    {
        return $this->belongsTo(GeneralLedger::class, 'gls_gl_code', 'gl_code');
    }
}