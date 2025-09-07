<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesAnalyst extends Model
{
    protected $primaryKey = 'analyst_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'analyst_name',
        'analyst_email',
        'password',   
    ];
}
