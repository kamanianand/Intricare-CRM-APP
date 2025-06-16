<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'gender', 'profile_image', 'additional_file', 'is_active'
    ];

    public function customValues()
    {
        return $this->hasMany(ContactCustomValue::class);
    }

    public function mergedAsMaster()
    {
        return $this->hasMany(MergedContact::class, 'master_contact_id');
    }

    public function mergedAsSecondary()
    {
        return $this->hasMany(MergedContact::class, 'merged_contact_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
