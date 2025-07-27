<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Telefone extends Model
{
    protected $table = 'telefones';
    protected $primaryKey = 'id_telefone';

    protected $fillable = [
        'ddd',
        'telefone',
        'whatsapp',
        'telegram',
        'principal',
        'id_fornecedor_fk'
    ];

    public function fornecedores(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor_fk');
    }

    use HasFactory;
}
