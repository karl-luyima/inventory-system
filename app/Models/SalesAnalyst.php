<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesAnalyst extends Model
{
    protected $primaryKey = 'analyst_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['user_id', 'analyst_email', 'analyst_name'];


    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reports() {
        return $this->hasMany(SalesReport::class, 'analyst_id');
    }
}
