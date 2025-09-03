<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $fillable = ['title', 'value', 'color'];
}
