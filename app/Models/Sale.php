<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $primaryKey = 'sales_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['pdt_id', 'quantity', 'totalAmount', 'date'];

    public function product() {
        return $this->belongsTo(Product::class, 'pdt_id');
    }
}
