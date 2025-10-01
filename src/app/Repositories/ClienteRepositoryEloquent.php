<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Models\CustomerSegment;
use App\Repositories\ClienteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Criteria\StatusCriteria;
class ClienteRepositoryEloquent extends BaseRepository implements ClienteRepository
{
    public function model()
    {
        return Cliente::class;
    }

   public function boot()
    {
        $this->pushCriteria(app(StatusCriteria::class));
        parent::boot();
    }

    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        $q         = (string) ($filters['q'] ?? '');
        $uf        = (string) ($filters['uf'] ?? '');
        $segmentId = $filters['segment_id'] ?? null;
        $status    = array_key_exists('status', $filters) ? $filters['status'] : null;

        $query = $this->model
            ->newQuery()
            ->when($q, fn($qry) => $qry->where(function($s) use ($q) {
                $s->where('nome','like',"%{$q}%")
                  ->orWhere('nome_fantasia','like',"%{$q}%")
                  ->orWhere('razao_social','like',"%{$q}%")
                  ->orWhere('documento','like',"%{$q}%")
                  ->orWhere('whatsapp','like',"%{$q}%")
                  ->orWhere('email','like',"%{$q}%");
            }))
            ->when($uf, fn($qry) => $qry->where('uf', $uf))
            ->when($segmentId, fn($qry) => $qry->where('segment_id', $segmentId))
            ->when(!is_null($status), fn($qry) => $qry->where('status', $status))
            ->with('segmento:id,nome')
            ->orderByDesc('id_cliente');

        $perPage = (int) ($filters['per_page'] ?? 10);

        $paginator = $query->paginate($perPage);
        // mantém filtros nos links de paginação
        return $paginator->appends($filters);
    }

    public function getSegments(): Collection
    {
        return CustomerSegment::select('id','nome')->orderBy('nome')->get();
    }

    public function createWithUser(array $data, int $userId): Cliente
    {
        $data['id_users_fk'] = $userId;
        /** @var Cliente $cliente */
        $cliente = $this->create($data); 
        return $cliente;
    }

    public function findWithRelations(int|string $id): Cliente
    {
        /** @var Cliente $cliente */
        $cliente = $this->with(['segmento:id,nome'])->find($id);
        return $cliente;
    }

    public function updateCliente(Cliente $cliente, array $data): bool
    {
        return $cliente->update($data);
    }

    public function deleteCliente(Cliente $cliente): bool
    {
        return (bool) $cliente->delete();
    }

    public function autocomplete(string $term, int $limit = 20): Collection
    {
        return $this->model
            ->newQuery()
            ->when($term, fn($q)=> $q->where(function($s) use ($term){
                $s->where('nome','like',"%{$term}%")
                  ->orWhere('nome_fantasia','like',"%{$term}%")
                  ->orWhere('razao_social','like',"%{$term}%")
                  ->orWhere('whatsapp','like',"%{$term}%");
            }))
            ->select('id_cliente','nome','nome_fantasia','razao_social','whatsapp','documento')
            ->orderBy('nome_fantasia')
            ->limit($limit)
            ->get()
            ->map(function($c){
                return [
                    'id'        => $c->id_cliente,
                    'label'     => $c->nome_fantasia ?: ($c->razao_social ?: ($c->nome ?: 'Cliente')),
                    'whatsapp'  => $c->whatsapp,
                    'documento' => $c->documento,
                ];
            });
    }

    public function ufs(): array
    {
        return ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
    }
    
}
