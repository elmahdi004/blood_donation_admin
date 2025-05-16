<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AvailableSlot extends Model
{
    use HasFactory;

    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }

    public function rendezVous() {
        return $this->hasOne(RendezVous::class, 'slot_id');
    }
}
