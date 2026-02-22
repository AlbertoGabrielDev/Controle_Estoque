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

        $modelName = Str::studly($model);
        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            abort(404, 'Model não encontrada');
        }

        $record = $modelClass::findOrFail($id);
        $record->toggleStatus();

        $statusColumn = method_exists($record, 'statusColumnName')
            ? $record->statusColumnName()
            : 'status';
        $newStatus = (int) ((bool) ($record->{$statusColumn} ?? 0));

        return response()->json([
            'status' => 200,
            'message' => 'Status atualizado com sucesso!',
            'new_status' => $newStatus,
            'type' => 'success'
        ]);
    }
}
