<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonStatus extends Component
{
    public $modelId;
    public $status;
    public $modelName;


 public function __construct($modelId, $status, $modelName)
{
    $this->modelId = $modelId;
    $this->status = $status;
    $this->modelName = $modelName; // Recebe diretamente o nome da model
}

    public function render()
    {
        return view('components.button-status');
    }

    public function shouldRender()
    {
        return auth()->check() && auth()->user()->canToggleStatus();
    }
}
