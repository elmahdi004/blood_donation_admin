<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposition extends Model
{
    protected $table = 'propositions';
    protected $primaryKey = 'proposition_id';
    protected $guarded = [];

    public function demande() {
        return $this->belongsTo(Demande::class,'demande_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
