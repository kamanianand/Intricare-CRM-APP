<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCustomValue extends Model
{
    protected $fillable = ['contact_id', 'field_id', 'field_value'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function field()
    {
        return $this->belongsTo(CustomField::class);
    }
}
