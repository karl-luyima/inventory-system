<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['password'];
    protected $hidden = ['password'];

    public function administrators() {
        return $this->hasMany(Administrator::class, 'user_id');
    }

    public function clerks() {
        return $this->hasMany(InventoryClerk::class, 'user_id');
    }

    public function analysts() {
        return $this->hasMany(SalesAnalyst::class, 'user_id');
    }
}
