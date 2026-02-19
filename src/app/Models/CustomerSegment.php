<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{

    use HasStatus;
    use HasDatatableConfig;
    protected $table = 'customer_segments';
    protected $fillable = ['nome'];
     public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'acoes' => ['computed' => true],
        ];
    }

    public static function dtFilters(): array
    {
        $t = (new static)->getTable();
        return [
            'q' => [
                'type' => 'text',
                'columns' => [
                    "{$t}.nome",
                ],
            ],
        ];
    }
}
