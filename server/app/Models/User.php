<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'date_de_naissance',
        'groupe_sanguin',
        'adresse',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_de_naissance' => 'date',
    ];

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function demandes() {
        return $this->hasMany(Demande::class);
    }

    public function propositions() {
        return $this->hasMany(Proposition::class);
    }

    public function reponseBesoins() {
        return $this->hasMany(ReponseBesoin::class);
    }

    public function intentionDons() {
        return $this->hasMany(IntentionDon::class);
    }

    public function rendezVous() {
        return $this->hasMany(RendezVous::class, 'user_id');
    }

    public function rendezVousCentre() {
        return $this->hasMany(RendezVous::class, 'centre_id');
    }

    public function stocks() {
        return $this->hasMany(Stock::class, 'centre_id');
    }

    public function availableSlots() {
        return $this->hasMany(AvailableSlot::class, 'centre_id');
    }
}
