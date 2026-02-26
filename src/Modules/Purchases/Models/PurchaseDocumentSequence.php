<?php

namespace Modules\Purchases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDocumentSequence extends Model
{
    use HasFactory;

    protected $table = 'purchase_document_sequences';

    protected $fillable = [
        'type',
        'last_number',
    ];

    protected $casts = [
        'last_number' => 'integer',
    ];
}
