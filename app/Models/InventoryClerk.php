<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryClerk extends Model
{
    protected $primaryKey = 'clerk_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['user_id', 'clerk_email', 'clerk_name'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
