<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntentionDon extends Model
{
    protected $fillable = [
        'groupe_sanguin',
        'date_disponibilite',
        'notes',
        'valide',
        'user_id',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
