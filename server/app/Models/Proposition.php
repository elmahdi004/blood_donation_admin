<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposition extends Model
{
    public function demande() {
        return $this->belongsTo(Demande::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
