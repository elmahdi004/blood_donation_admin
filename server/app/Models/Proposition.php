<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proposition extends Model
{
    use HasFactory;

    public function demande() {
        return $this->belongsTo(Demande::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
