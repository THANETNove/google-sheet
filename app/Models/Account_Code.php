<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account_Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'acc_code_company',
        'acc_code',
        'acc_name',
        'acc_type'
    ];
}
