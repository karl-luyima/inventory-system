<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    protected $primaryKey = 'report_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['analyst_id', 'generateDate'];

    public function analyst() {
        return $this->belongsTo(SalesAnalyst::class, 'analyst_id');
    }
}
