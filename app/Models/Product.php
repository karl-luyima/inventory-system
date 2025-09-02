<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'pdt_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['pdt_name', 'price', 'stock_level', 'inventory_id'];

    public function inventory() {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function sales() {
        return $this->hasMany(Sale::class, 'pdt_id');
    }
}
