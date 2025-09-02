<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    protected $primaryKey = 'adminID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['user_id', 'admin_email', 'admin_name'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
