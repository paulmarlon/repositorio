<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class ModelHasRoleGestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'model_has_role_gestions';

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
        'gestion_id',
    ];

    // Relación con el Rol de Spatie
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Relación con el Usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'model_id');
    }

    // Relación con la Gestión Académica
    public function gestion(): BelongsTo
    {
        return $this->belongsTo(GestionAcademica::class, 'gestion_id'); // Ajusta el nombre del modelo de gestiones si difiere
    }
}
