<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'account_name',
        'number',
        'qrcode_image',
        'bank_name',
        'active'
    ];
}
