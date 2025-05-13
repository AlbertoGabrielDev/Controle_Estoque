<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function updateStatus($model, $id)
    {
    
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($model));
        
        if (!class_exists($modelClass)) {
            abort(404, 'Model não encontrada');
        }

        $record = $modelClass::findOrFail($id);
        $record->toggleStatus();

        return response()->json([
            'status' => 200,
            'message' => 'Status atualizado com sucesso!',
            'new_status' => $record->status,
            'type' => 'success'
        ]);
    }
}
