<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'medical_name',
        'phone_number',
        'units',
        'blood_group',
        'donation_date',
        'donation_time',
        'district',
        'area',
        'user_id',
        'status'
    ];

    protected $casts = [
        'donation_date' => 'date',
        'units' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 