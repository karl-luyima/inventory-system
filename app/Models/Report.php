<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['name', 'creator_type', 'creator_id', 'data'];
}
