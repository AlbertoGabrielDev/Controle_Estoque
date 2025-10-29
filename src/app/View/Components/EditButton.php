<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class EditButton extends Component
{
    /** @var string */
    public string $route;

    /** @var mixed */
    public $modelId;

    /** @var string|null */
    public ?string $menuSlug;

    /** @var string */
    public string $permission;

    /**
     * @param  string       $route       Nome da rota (ex.: 'fornecedor.editar')
     * @param  mixed        $modelId     ID do modelo
     * @param  string|null  $menuSlug    Slug do menu (ex.: 'fornecedores'). Se null, tentamos inferir.
     * @param  string       $permission  Nome da permissão (default: 'edit_post')
     */
    public function __construct(string $route, $modelId, ?string $menuSlug = null, string $permission = 'edit_post')
    {
        $this->route      = $route;
        $this->modelId    = $modelId;
        $this->menuSlug   = $menuSlug;
        $this->permission = $permission;
    }

    public function render()
    {
        return view('components.edit-button');
    }

    public function shouldRender(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $slug = $this->resolveMenuSlug();

        // Se ainda não conseguimos um slug válido, não renderiza
        if (!$slug) {
            return false;
        }

        return auth()->user()->hasPermission($slug, $this->permission);
    }

    /**
     * Resolve o slug do menu por ordem de prioridade:
     * 1) A prop $menuSlug vinda do Blade
     * 2) Mapa de prefixos de rota -> slug (config/menus.php)
     * 3) Prefixo do nome da rota como slug (fallback).
     */
    protected function resolveMenuSlug(): ?string
    {
        if (!empty($this->menuSlug)) {
            return $this->menuSlug;
        }

        $routeName = optional(request()->route())->getName(); 
        if (!$routeName) {
            return request()->attributes->get('currentMenuSlug');
        }

        $prefix = Str::before($routeName, '.');
        $map    = config('menus.route_prefix_to_menu_slug', []);

        if (!empty($map[$prefix])) {
            return $map[$prefix];
        }
        return $prefix ?: request()->attributes->get('currentMenuSlug');
    }
}
