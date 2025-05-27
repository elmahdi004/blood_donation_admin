<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'max_appointments',
        'blood_bank_id',
    ];

    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }

    public function rendezVous() {
        return $this->hasOne(RendezVous::class, 'slot_id');
    }
}
