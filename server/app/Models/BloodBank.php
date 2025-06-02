<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BloodBank extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'address',
        'region',
        'phone',
        'email',
        'password',
        'opening_hours',
        'latitude',
        'longitude',
        'status',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'email_verified_at' => 'datetime',
    ];

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function inventory()
    {
        return $this->hasMany(BloodInventory::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class,'centre_id');
    }
    public function rendezVousCentre()
    {
        return $this->hasMany(RendezVous::class,'centre_id');
    }
    public function slots()
    {
        return $this->hasMany(AvailableSlot::class,'centre_id');
    }
    
} 