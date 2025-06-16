<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['field_name', 'field_type', 'field_options'];

    protected $casts = [
        'field_options' => 'array'
    ];
}
