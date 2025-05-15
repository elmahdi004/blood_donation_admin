<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }

    public function slot() {
        return $this->belongsTo(AvailableSlot::class, 'slot_id');
    }
}
