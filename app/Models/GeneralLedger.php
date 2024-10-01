<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
{
    use HasFactory;
    protected $fillable = [
        'gl_code_company',
        'gl_code',
        'gl_refer',
        'gl_report_vat',
        'gl_date',
        'gl_document',
        'gl_date_check',
        'gl_document_check',
        'gl_company',
        'gl_taxid',
        'gl_branch',
        'gl_code_acc',
        'gl_description',
        'gl_code_acc_pay',
        'gl_date_pay',
        'gl_vat',
        'gl_rate',
        'gl_taxmonth',
        'gl_amount_no_vat',
        'gl_amount',
        'gl_tax',
        'gl_total',
        'gl_url',
        'gl_page',
        'gl_remark',
        'gl_email',
    ];
}
