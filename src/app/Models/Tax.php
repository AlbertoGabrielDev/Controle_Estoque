<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'taxes';
    protected $fillable = ['codigo','nome','ativo'];
    protected $casts = ['ativo' => 'boolean'];

    public function rules()
    {
        return $this->hasMany(TaxRule::class, 'tax_id');
    }
}
