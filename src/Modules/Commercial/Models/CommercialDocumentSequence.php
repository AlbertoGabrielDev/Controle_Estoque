<?php

namespace Modules\Commercial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialDocumentSequence extends Model
{
    use HasFactory;

    protected $table = 'commercial_document_sequences';

    protected $fillable = [
        'type',
        'last_number',
    ];

    protected $casts = [
        'last_number' => 'integer',
    ];
}
