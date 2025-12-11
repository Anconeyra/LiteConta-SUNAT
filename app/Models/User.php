<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id', // Importante: Agregado para relacionar con la empresa
        'name',
        'email',
        'password',
        'role',       // Importante: Agregado para definir si es admin o digitador
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /* RELACIONES                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Relación: Un usuario pertenece a una Empresa (Company).
     * Esto te permitirá usar: Auth::user()->company->razon_social
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}