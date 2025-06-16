<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MergedContact extends Model
{
    protected $fillable = ['master_contact_id', 'merged_contact_id', 'merged_data'];

    protected $casts = [
        'merged_data' => 'array'
    ];

    public function masterContact()
    {
        return $this->belongsTo(Contact::class, 'master_contact_id');
    }

    public function mergedContact()
    {
        return $this->belongsTo(Contact::class, 'merged_contact_id');
    }
}
