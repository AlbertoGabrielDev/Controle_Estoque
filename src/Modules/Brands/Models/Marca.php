<?php

namespace Modules\Brands\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Brands\Database\Factories\MarcaFactory;
use Modules\Products\Models\Produto;
use Modules\Stock\Models\Estoque;

class Marca extends Model
{
    use HasStatus;
    use HasDatatableConfig;
    use HasFactory;

    protected $table = 'marcas';

    protected $primaryKey = 'id_marca';

    protected $fillable = [
        'nome_marca',
        'id_users_fk',
    ];

    public function produto(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'marca_produtos', 'id_marca_fk', 'id_marca')
            ->as('marca_produto');
    }

    public function estoques(): BelongsToMany
    {
        return $this->belongsToMany(Estoque::class, 'marcas', 'id_marca');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();

        return [
            'id' => ['db' => "{$t}.id_marca", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.nome_marca", 'label' => 'Marca', 'order' => true, 'search' => true],
            'st' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => false],
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
                    "{$t}.nome_marca",
                ],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
        ];
    }

    protected static function newFactory()
    {
        return MarcaFactory::new();
    }
}
