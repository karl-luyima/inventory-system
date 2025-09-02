<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $primaryKey = 'inventory_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['pdtList', 'inventory_name'];

    public function products() {
        return $this->hasMany(Product::class, 'inventory_id');
    }
}
