<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataGeneralLedgerSub extends Model
{
    use HasFactory;

    // กำหนดชื่อของตาราง (ถ้าตารางในฐานข้อมูลชื่ออื่นจากชื่อโมเดล)
    protected $table = 'general_ledgers';

    // กำหนดคีย์หลัก (Primary Key) ของตาราง
    protected $primaryKey = 'gl_code';  // กำหนดฟิลด์ gl_code เป็น Primary Key ถ้าไม่ใช่ id

    // กำหนดว่าคีย์หลักนี้ไม่ใช่แบบ auto increment
    public $incrementing = false;

    // กำหนดชนิดของคีย์หลัก
    protected $keyType = 'string';


    /**
     * Fetch related general_ledger_subs records based on gl_code.
     *
     * @param string $gl_code
     * @return \Illuminate\Support\Collection
     */
    public function subs()
    {
        return $this->hasMany(GeneralLedgerSub::class, 'gls_gl_code', 'gl_code');
    }
}
