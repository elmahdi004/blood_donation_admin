<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableSlot extends Model
{
    use HasFactory;

    protected $table = 'available_slots';
    protected $primaryKey = 'slot_id';

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'max_appointments',
        'centre_id'
    ];

    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }

    public function rendezVous()
    {
        return $this->hasOne(RendezVous::class, 'slot_id');
    }
}
