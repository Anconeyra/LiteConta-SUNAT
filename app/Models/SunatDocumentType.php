<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SunatDocumentType extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false; 

    public function documents()
    {
        return $this->hasMany(Document::class, 'sunat_type_id');
    }
}