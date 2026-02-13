<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentItem extends Model
{
    protected $fillable = ['document_id', 'description', 'quantity', 'unit_price', 'total'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}