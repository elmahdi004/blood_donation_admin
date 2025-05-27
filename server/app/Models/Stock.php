<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['groupe_sanguin', 'quantite'];
    protected $primaryKey = 'stock_id';
    public function centre() {
        return $this->belongsTo(User::class, 'centre_id');
    }
}
