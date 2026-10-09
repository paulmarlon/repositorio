<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nombres',
        'paterno',
        'materno',
        'ci',
        'email',
        'password',
        'avatar',
        'activo',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->nombres}",
        );
    }


    // Dentro de tu clase User:

    public function adminlte_image(): string
    {
        // Si tiene foto subida, retorna la ruta pública
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Si no tiene foto, genera automáticamente las iniciales con UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nombres) . '&background=0D8ABC&color=fff&size=160';
    }

    public function adminlte_desc(): string
    {
        return 'Miembro activo'; // O puedes poner el rol, correo, etc.
    }

    public function adminlte_profile_url(): string
    {
        return '#'; // O la ruta a tu vista de perfil si la creas luego: route('profile')
    }
}
