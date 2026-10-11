<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class TrabajoGrado extends Model
{
    use HasFactory, SoftDeletes, HasRoles;

    protected $table = 'trabajos_grado';

    protected $fillable = [
        'autor_id',
        'supervisor_id',
        'tipo_id',
        'categoria_id',
        'gestion_id',
        'titulo',
        'resumen',
        'archivo_url',
        'estado',
        'puntaje',
        'motivo_rechazo',
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tipoTrabajo(): BelongsTo
    {
        return $this->belongsTo(TipoTrabajo::class, 'tipo_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(GestionAcademica::class, 'gestion_id');
    }
}
