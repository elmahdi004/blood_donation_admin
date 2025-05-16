<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Demande extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function propositions() {
        return $this->hasMany(Proposition::class);
    }

    public function reponseBesoins() {
        return $this->hasMany(ReponseBesoin::class);
    }
}
