<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $fillable = [
        'title',
        'description',
        'url',
        'body',
        'status_date',
        'status_body',
        'status_detail'
    ];
}
