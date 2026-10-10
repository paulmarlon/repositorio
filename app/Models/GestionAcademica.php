<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GestionAcademica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gestion_academicas';

    protected $fillable = [
        'anio',
        'periodo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
