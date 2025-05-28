<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RendezVous extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $table = 'rendez_vouses';
    protected $primaryKey = 'rendez_vous_id';

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
