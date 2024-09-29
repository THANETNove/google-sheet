<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_company',
        'company',
        'branch',
        'tax_id',
        'id_sheet',
        'id_apps_script',
    ];
}