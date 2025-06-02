<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $guarded = [];
    protected $table = 'demandes';
    protected $primaryKey = 'demande_id';
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function propositions() {
        return $this->hasMany(Proposition::class,'demande_id');
    }

    public function reponseBesoins() {
        return $this->hasMany(ReponseBesoin::class);
    }
}
