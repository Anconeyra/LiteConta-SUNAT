<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Pertenece a una empresa específica
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Un socio puede estar asociado a muchos documentos (Facturas recibidas o emitidas)
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Un socio puede tener una regla de clasificación automática (ej: Sodimac -> Mantenimiento)
    public function classificationRule()
    {
        return $this->hasOne(ClassificationRule::class);
    }
}