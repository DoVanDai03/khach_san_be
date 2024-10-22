<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoDich extends Model
{
    use HasFactory;
    protected $table = 'giao_diches';

    protected $fillable = [
        'creditAmount',
        'description',
        'refNo',
    ];
}
