<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditButton extends Component
{
    public $route;
    public $modelId;

    public function __construct($route, $modelId)
    {
        $this->route = $route;
        $this->modelId = $modelId;
    }

    public function render()
    {
        return view('components.edit-button');
    }

    public function shouldRender()
    {
        return auth()->check() && 
               auth()->user()->hasPermission(
                   request()->attributes->get('currentMenuSlug'),
                   'edit_post'
               );
    }

    
}
