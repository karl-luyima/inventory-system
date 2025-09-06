<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    
    protected $primaryKey = 'admin_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    protected $fillable = [
        'admin_name',
        'admin_email',
        'password',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
