<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use HasFactory;
    
    protected $fillable = ['key', 'value', 'label', 'type', 'options'];

    protected $casts = [
        'options' => 'array',
    ];
}
