<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relación con Usuarios (Una empresa tiene varios usuarios)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relación con Documentos (Una empresa emite muchas facturas/boletas)
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Relación con Socios (Clientes y Proveedores)
    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    // Relación con Categorías contables
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // Relación con Reglas de clasificación automática
    public function classificationRules()
    {
        return $this->hasMany(ClassificationRule::class);
    }
}