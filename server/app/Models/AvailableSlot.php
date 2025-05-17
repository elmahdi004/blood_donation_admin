<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableSlot extends Model
{
    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }

    public function rendezVous() {
        return $this->hasOne(RendezVous::class, 'slot_id');
    }
}
