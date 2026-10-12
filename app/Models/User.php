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
use App\Models\GestionAcademica;
use App\Models\ModelHasRoleGestion;


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
    public function rolesPorGestion()
    {
        return $this->hasMany(ModelHasRoleGestion::class, 'model_id');
    }
    public function hasRoleInActiveGestion(string $roleName): bool
    {
        // 1. Si el usuario es Administrador global por Spatie o es el ID 1, permitir acceso total siempre
        if ($this->hasRole('Administrador') || $this->hasRole('administrador') || $this->id === 1) {
            return true;
        }

        $gestionActiva = GestionAcademica::where('activo', true)->first();
        if (!$gestionActiva) {
            return false;
        }

        // 2. Verificar en la tabla de roles por gestión para otros roles (como Supervisor)
        return ModelHasRoleGestion::where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->where('gestion_id', $gestionActiva->id)
            ->whereHas('role', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->exists();
    }
}
