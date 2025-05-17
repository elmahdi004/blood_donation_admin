<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }
}
