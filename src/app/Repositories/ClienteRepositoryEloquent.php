<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Models\CustomerSegment;
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
        $q         = trim((string) ($filtewrs['q'] ?? ''));
        $uf        = strtoupper(trim((string) ($filters['uf'] ?? '')));
        $segmentId = $filters['segment_id'] ?? null;
        $raw    = $filters['status'] ?? 1;
        $status = ((string)$raw === '1' || $raw === 1) ? 1 : 0;

        $query = $this->model
            ->newQuery()
            ->when($q !== '', fn($qry) => $qry->where(function($s) use ($q) {
                $s->where('nome','like',"%{$q}%")
                  ->orWhere('nome_fantasia','like',"%{$q}%")
                  ->orWhere('razao_social','like',"%{$q}%")
                  ->orWhere('documento','like',"%{$q}%")
                  ->orWhere('whatsapp','like',"%{$q}%")
                  ->orWhere('email','like',"%{$q}%");
            }))
            ->when($uf !== '', fn($qry) => $qry->whereRaw('UPPER(uf) = ?', [$uf]))
            ->when(!is_null($segmentId), fn($qry) => $qry->where('segment_id', $segmentId))
            ->when(!is_null($status), fn($qry) => $qry->where('status', $status))
            ->with('segmento:id,nome')
            ->orderByDesc('id_cliente');

        $perPage = (int) ($filters['per_page'] ?? 10);
        $paginator = $query->paginate($perPage);

        return $paginator->appends($filters);
    }

    public function getSegments(): Collection
    {
        return CustomerSegment::select('id','nome')->orderBy('nome')->get();
    }

    public function createCliente(array $data, int $userId): Cliente
    {
        $data['id_users_fk'] = $userId;
        $cliente = $this->create($data);
        return $cliente;
    }

    public function updateCliente(int|string $id, array $data): Cliente
    {
        $updated = $this->update($data, $id); 
        return $updated;
    }

    public function findWithRelations(int|string $id): Cliente
    {
        $cliente = $this->with(['segmento:id,nome'])->find($id);
        return $cliente;
    }

    public function deleteCliente(int|string $id): bool
    {
        return (bool) $this->delete($id);
    }

    public function autocomplete(string $term, int $limit = 20): Collection
    {
        return $this->model
            ->newQuery()
            ->when($term !== '', fn($q)=> $q->where(function($s) use ($term){
                $s->where('nome','like',"%{$term}%")
                  ->orWhere('nome_fantasia','like',"%{$term}%")
                  ->orWhere('razao_social','like',"%{$term}%")
                  ->orWhere('whatsapp','like',"%{$term}%");
            }))
            ->select('id_cliente','nome','nome_fantasia','razao_social','whatsapp','documento')
            ->orderByRaw('COALESCE(nome_fantasia, razao_social, nome) ASC')
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
